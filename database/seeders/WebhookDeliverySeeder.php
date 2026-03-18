<?php

namespace Database\Seeders;

use App\Enums\WebhookEventType;
use App\Enums\WebhookStatus;
use App\Models\Merchant;
use App\Models\Transaction;
use App\Models\WebhookDelivery;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class WebhookDeliverySeeder extends Seeder
{
  public function run(): void
  {
    $merchants = Merchant::all();

    foreach ($merchants as $merchant) {
      $transactions = Transaction::where('merchant_id', $merchant->id)
        ->inRandomOrder()
        ->limit(30)
        ->get();

      foreach ($transactions as $index => $tx) {

      if (!$merchant->webhook_url) continue;
        $createdAt = $tx->created_at->copy()->addSeconds(rand(1, 5));

        // Most deliveries succeed
        $status    = $index < 24 ? WebhookStatus::Delivered : WebhookStatus::Failed;
        $httpStatus = $status === WebhookStatus::Delivered ? 200 : 500;
        $attempts   = $status === WebhookStatus::Failed ? 3 : 1;

        WebhookDelivery::create([
          'merchant_id'    => $merchant->id,
          'transaction_id' => $tx->id,
          'event_type'     => $tx->decision->value === 'decline'
            ? WebhookEventType::TransactionDeclined->value
            : WebhookEventType::TransactionScored->value,
          'payload'        => [
            'event'          => 'transaction.scored',
            'transaction_id' => $tx->ulid,
            'decision'       => $tx->decision->value,
            'risk_score'     => $tx->risk_score,
          ],
          'url'            => $merchant->webhook_url,
          'http_status'    => $httpStatus,
          'response_body'  => $status === WebhookStatus::Delivered
            ? '{"received":true}'
            : 'Internal Server Error',
          'attempt_number' => $attempts,
          'status'         => $status->value,
          'next_retry_at'  => null,
          'created_at'     => $createdAt,
          'updated_at'     => $createdAt,
        ]);
      }

      // Add 2 retrying deliveries
      $retryTx = Transaction::where('merchant_id', $merchant->id)
        ->inRandomOrder()
        ->first();

      if ($retryTx && $merchant->webhook_url) {
        WebhookDelivery::create([
          'merchant_id'    => $merchant->id,
          'transaction_id' => $retryTx->id,
          'event_type'     => WebhookEventType::TransactionScored->value,
          'payload'        => ['event' => 'transaction.scored'],
          'url'            => $merchant->webhook_url,
          'http_status'    => 503,
          'response_body'  => 'Service Unavailable',
          'attempt_number' => 2,
          'status'         => WebhookStatus::Retrying->value,
          'next_retry_at'  => Carbon::now()->addMinutes(5),
          'created_at'     => Carbon::now()->subMinutes(1),
          'updated_at'     => Carbon::now()->subMinutes(1),
        ]);
      }
    }

    $this->command->info('✓ Webhook deliveries seeded');
  }
}
