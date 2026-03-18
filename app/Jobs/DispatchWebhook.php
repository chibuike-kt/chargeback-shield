<?php

namespace App\Jobs;

use App\Enums\WebhookStatus;
use App\Models\WebhookDelivery;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DispatchWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Max attempts handled manually inside the job
     * so we control retry timing precisely.
     */
    public int $tries = 1;

    public function __construct(
        private int $deliveryId,
    ) {}

    public function handle(): void
    {
        $delivery = WebhookDelivery::find($this->deliveryId);

        if (!$delivery) {
            Log::warning('[Webhook] Delivery record not found', [
                'delivery_id' => $this->deliveryId,
            ]);
            return;
        }

        // Skip if already delivered
        if ($delivery->status === WebhookStatus::Delivered) {
            return;
        }

        $merchant = $delivery->merchant;

        // Build HMAC signature for payload verification
        $payloadJson = json_encode($delivery->payload);
        $signature   = hash_hmac('sha256', $payloadJson, $merchant->webhook_secret);

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Content-Type'            => 'application/json',
                    'X-Chargeback-Shield-Sig' => 'sha256=' . $signature,
                    'X-Event-Type'            => $delivery->event_type->value,
                    'X-Delivery-ID'           => $delivery->ulid,
                    'User-Agent'              => 'ChargebackShield/1.0',
                ])
                ->post($delivery->url, $delivery->payload);

            $this->recordSuccess($delivery, $response->status(), $response->body());
        } catch (\Exception $e) {
            $this->recordFailureAndRetry($delivery, null, $e->getMessage());
        }
    }

    private function recordSuccess(
        WebhookDelivery $delivery,
        int $httpStatus,
        string $responseBody
    ): void {
        // Consider 2xx as success
        $isSuccess = $httpStatus >= 200 && $httpStatus < 300;

        if ($isSuccess) {
            $delivery->update([
                'status'        => WebhookStatus::Delivered->value,
                'http_status'   => $httpStatus,
                'response_body' => substr($responseBody, 0, 1000),
                'next_retry_at' => null,
            ]);

            Log::info('[Webhook] Delivered successfully', [
                'delivery_id' => $delivery->ulid,
                'url'         => $delivery->url,
                'status'      => $httpStatus,
            ]);
        } else {
            // Non-2xx response counts as failure
            $this->recordFailureAndRetry($delivery, $httpStatus, $responseBody);
        }
    }

    private function recordFailureAndRetry(
        WebhookDelivery $delivery,
        ?int $httpStatus,
        string $errorBody
    ): void {
        $attempt = $delivery->attempt_number;

        Log::warning('[Webhook] Delivery failed', [
            'delivery_id' => $delivery->ulid,
            'attempt'     => $attempt,
            'http_status' => $httpStatus,
            'error'       => substr($errorBody, 0, 200),
        ]);

        // Max 3 attempts
        if ($attempt >= 3) {
            $delivery->update([
                'status'        => WebhookStatus::Failed->value,
                'http_status'   => $httpStatus,
                'response_body' => substr($errorBody, 0, 1000),
                'next_retry_at' => null,
            ]);

            Log::error('[Webhook] Permanently failed after 3 attempts', [
                'delivery_id' => $delivery->ulid,
                'url'         => $delivery->url,
            ]);

            return;
        }

        // Exponential backoff: attempt 1=1min, 2=5min, 3=15min
        $delayMinutes = [1 => 1, 2 => 5, 3 => 15];
        $delay        = $delayMinutes[$attempt] ?? 15;
        $nextRetryAt  = now()->addMinutes($delay);

        $delivery->update([
            'status'         => WebhookStatus::Retrying->value,
            'http_status'    => $httpStatus,
            'response_body'  => substr($errorBody, 0, 1000),
            'attempt_number' => $attempt + 1,
            'next_retry_at'  => $nextRetryAt,
        ]);

        // Schedule the retry
        self::dispatch($delivery->id)->delay($nextRetryAt);
    }
}
