<?php

namespace App\Actions\Transactions;

use App\Models\EvidenceBundle;
use App\Models\Merchant;
use App\Models\Transaction;
use App\Services\EvidenceService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateEvidenceBundle
{
  public function __construct(
    private EvidenceService $evidenceService,
  ) {}

  /**
   * Build, sign, encrypt, and store an evidence bundle
   * for an approved transaction.
   *
   * This is called immediately after a transaction is approved.
   * The bundle is write-once — it cannot be modified after creation.
   */
  public function execute(Transaction $transaction, Merchant $merchant): EvidenceBundle
  {
    // Load signal logs for this transaction
    $signals = $transaction->riskSignalLogs()
      ->get()
      ->map(fn($s) => [
        'signal_name'           => $s->signal_name,
        'raw_value'             => $s->raw_value,
        'normalized_score'      => $s->normalized_score,
        'weight'                => $s->weight,
        'weighted_contribution' => $s->weighted_contribution,
      ])
      ->toArray();

    // Build the full evidence payload
    $payload = $this->buildPayload($transaction, $merchant, $signals);

    // Sign the payload with merchant's webhook secret
    $signature = $this->evidenceService->sign($payload, $merchant->webhook_secret);

    // Encrypt the payload
    ['encrypted' => $encrypted, 'iv' => $iv] = $this->evidenceService->encrypt(
      $payload,
      $merchant->webhook_secret
    );

    // Store the bundle — immutable from this point forward
    return DB::transaction(function () use (
      $transaction,
      $merchant,
      $encrypted,
      $iv,
      $signature
    ) {
      $bundle = new EvidenceBundle([
        'transaction_id'    => $transaction->id,
        'merchant_id'       => $merchant->id,
        'payload_encrypted' => $encrypted,
        'encryption_iv'     => $iv,
        'hmac_signature'    => $signature,
        'is_verified'       => true,
      ]);

      // This calls our overridden save() which blocks updates
      $bundle->save();

      // Link transaction to evidence bundle
      Transaction::where('id', $transaction->id)
        ->update(['evidence_bundle_id' => $bundle->id]);

      Log::info('Evidence bundle created', [
        'bundle_id'      => $bundle->ulid,
        'transaction_id' => $transaction->ulid,
        'merchant_id'    => $merchant->id,
      ]);

      return $bundle;
    });
  }

  /**
   * Build the complete evidence payload.
   * This is the canonical record of everything that was known
   * at the time the transaction was approved.
   */
  private function buildPayload(
    Transaction $transaction,
    Merchant $merchant,
    array $signals
  ): array {
    return [
      'evidence_version'   => '1.0',
      'transaction'        => [
        'id'               => $transaction->ulid,
        'idempotency_key'  => $transaction->idempotency_key,
        'amount'           => $transaction->amount,
        'currency'         => $transaction->currency,
        'status'           => $transaction->status->value,
        'decision'         => $transaction->decision->value,
        'created_at'       => $transaction->created_at->toIso8601String(),
      ],
      'card'               => [
        'bin'              => $transaction->card_bin,
        'last4'            => $transaction->card_last4,
        'country'          => $transaction->card_country,
      ],
      'network'            => [
        'ip_address'       => $transaction->ip_address,
        'ip_country'       => $transaction->ip_country,
        'ip_city'          => $transaction->ip_city,
      ],
      'device'             => [
        'fingerprint'      => $transaction->device_fingerprint,
        'session_token'    => $transaction->session_token,
        'session_age_seconds' => $transaction->session_age_seconds,
      ],
      'merchant'           => [
        'id'               => $merchant->ulid,
        'company_name'     => $merchant->company_name,
        'category'         => $transaction->merchant_category,
      ],
      'risk'               => [
        'score'            => $transaction->risk_score,
        'level'            => $transaction->risk_level->value,
        'signals'          => $signals,
      ],
      'locked_at'          => now()->toIso8601String(),
    ];
  }
}
