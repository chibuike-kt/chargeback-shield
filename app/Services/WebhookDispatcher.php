<?php

namespace App\Services;

use App\Enums\WebhookEventType;
use App\Enums\WebhookStatus;
use App\Jobs\DispatchWebhook;
use App\Models\Dispute;
use App\Models\Merchant;
use App\Models\Transaction;
use App\Models\WebhookDelivery;
use Illuminate\Support\Facades\Log;

class WebhookDispatcher
{
  /**
   * Fire a transaction scored webhook.
   */
  public function transactionScored(Transaction $transaction, Merchant $merchant): void
  {
    if (!$merchant->webhook_url) {
      return;
    }

    $payload = [
      'event'          => WebhookEventType::TransactionScored->value,
      'transaction_id' => $transaction->ulid,
      'decision'       => $transaction->decision->value,
      'risk_score'     => $transaction->risk_score,
      'risk_level'     => $transaction->risk_level->value,
      'amount'         => $transaction->amount,
      'currency'       => $transaction->currency,
      'card_last4'     => $transaction->card_last4,
      'card_bin'       => $transaction->card_bin,
      'status'         => $transaction->status->value,
      'has_evidence'   => $transaction->hasEvidence(),
      'timestamp'      => $transaction->created_at->toIso8601String(),
    ];

    $this->dispatch(
      $merchant,
      WebhookEventType::TransactionScored,
      $payload,
      $transaction->id,
      null
    );
  }

  /**
   * Fire a high-risk detected webhook for post-auth transactions.
   * This tells the fintech a transaction they already approved
   * scored dangerously high — they can freeze the card or
   * flag the account.
   */
  public function highRiskDetected(Transaction $transaction, Merchant $merchant): void
  {
    if (!$merchant->webhook_url) {
      return;
    }

    $payload = [
      'event'          => 'transaction.high_risk_detected',
      'transaction_id' => $transaction->ulid,
      'risk_score'     => $transaction->risk_score,
      'risk_level'     => $transaction->risk_level->value,
      'amount'         => $transaction->amount,
      'currency'       => $transaction->currency,
      'card_last4'     => $transaction->card_last4,
      'card_bin'       => $transaction->card_bin,
      'ip_country'     => $transaction->ip_country,
      'card_country'   => $transaction->card_country,
      'message'        => 'This transaction scored above the high-risk threshold after authorization. Consider freezing the card or flagging the account.',
      'timestamp'      => now()->toIso8601String(),
    ];

    $this->dispatch(
      $merchant,
      \App\Enums\WebhookEventType::TransactionDeclined, // reuse declined event type
      $payload,
      $transaction->id,
      null
    );
  }

  /**
   * Fire a transaction declined webhook.
   */
  public function transactionDeclined(Transaction $transaction, Merchant $merchant): void
  {
    if (!$merchant->webhook_url) {
      return;
    }

    $payload = [
      'event'          => WebhookEventType::TransactionDeclined->value,
      'transaction_id' => $transaction->ulid,
      'decision'       => $transaction->decision->value,
      'risk_score'     => $transaction->risk_score,
      'risk_level'     => $transaction->risk_level->value,
      'amount'         => $transaction->amount,
      'currency'       => $transaction->currency,
      'card_last4'     => $transaction->card_last4,
      'timestamp'      => $transaction->created_at->toIso8601String(),
    ];

    $this->dispatch(
      $merchant,
      WebhookEventType::TransactionDeclined,
      $payload,
      $transaction->id,
      null
    );
  }

  /**
   * Fire a dispute filed webhook.
   */
  public function disputeFiled(Dispute $dispute, Merchant $merchant): void
  {
    if (!$merchant->webhook_url) {
      return;
    }

    $payload = [
      'event'          => WebhookEventType::DisputeFiled->value,
      'dispute_id'     => $dispute->ulid,
      'transaction_id' => $dispute->transaction->ulid,
      'reason_code'    => $dispute->reason_code,
      'network'        => $dispute->network->value,
      'status'         => $dispute->status->value,
      'response_ready' => !is_null($dispute->response_document),
      'amount'         => $dispute->transaction->amount,
      'currency'       => $dispute->transaction->currency,
      'timestamp'      => now()->toIso8601String(),
    ];

    $this->dispatch(
      $merchant,
      WebhookEventType::DisputeFiled,
      $payload,
      $dispute->transaction_id,
      $dispute->id
    );
  }

  /**
   * Fire a dispute resolved webhook.
   */
  public function disputeResolved(
    Dispute $dispute,
    Merchant $merchant,
    string $outcome
  ): void {
    if (!$merchant->webhook_url) {
      return;
    }

    $eventType = $outcome === 'won'
      ? WebhookEventType::DisputeWon
      : WebhookEventType::DisputeLost;

    $payload = [
      'event'          => $eventType->value,
      'dispute_id'     => $dispute->ulid,
      'transaction_id' => $dispute->transaction->ulid,
      'reason_code'    => $dispute->reason_code,
      'network'        => $dispute->network->value,
      'outcome'        => $outcome,
      'amount'         => $dispute->transaction->amount,
      'currency'       => $dispute->transaction->currency,
      'timestamp'      => now()->toIso8601String(),
    ];

    $this->dispatch(
      $merchant,
      $eventType,
      $payload,
      $dispute->transaction_id,
      $dispute->id
    );
  }

  /**
   * Core dispatch — creates delivery record and queues the job.
   */
  private function dispatch(
    Merchant         $merchant,
    WebhookEventType $eventType,
    array            $payload,
    ?int             $transactionId,
    ?int             $disputeId
  ): void {
    try {
      $delivery = WebhookDelivery::create([
        'merchant_id'    => $merchant->id,
        'transaction_id' => $transactionId,
        'dispute_id'     => $disputeId,
        'event_type'     => $eventType->value,
        'payload'        => $payload,
        'url'            => $merchant->webhook_url,
        'status'         => WebhookStatus::Pending->value,
        'attempt_number' => 1,
      ]);

      DispatchWebhook::dispatch($delivery->id);
    } catch (\Exception $e) {
      Log::error('[WebhookDispatcher] Failed to create delivery record', [
        'merchant_id' => $merchant->id,
        'event_type'  => $eventType->value,
        'error'       => $e->getMessage(),
      ]);
    }
  }

  /**
   * Manually re-trigger a failed webhook delivery.
   */
  public function retrigger(WebhookDelivery $delivery): void
  {
    $delivery->update([
      'status'         => WebhookStatus::Retrying->value,
      'attempt_number' => 1,
      'next_retry_at'  => null,
    ]);

    DispatchWebhook::dispatch($delivery->id);
  }
}
