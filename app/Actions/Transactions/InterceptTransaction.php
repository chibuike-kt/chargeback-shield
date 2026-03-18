<?php

namespace App\Actions\Transactions;

use App\Enums\ActorType;
use App\Enums\DecisionType;
use App\Models\AuditLog;
use App\Models\Merchant;
use App\Services\IdempotencyService;
use App\Services\ScoringService;
use App\Services\TransactionService;
use App\Services\WebhookDispatcher;
use Illuminate\Support\Facades\Log;

class InterceptTransaction
{
  public function __construct(
    private IdempotencyService   $idempotency,
    private ScoringService       $scorer,
    private TransactionService   $transactionService,
    private CreateEvidenceBundle $createEvidenceBundle,
    private WebhookDispatcher    $webhookDispatcher,
  ) {}

  public function execute(Merchant $merchant, array $data): array
  {
    $idempotencyKey = $data['idempotency_key'];

    // ── Idempotency check ─────────────────────────────────────────────────
    $cached = $this->idempotency->get((string) $merchant->id, $idempotencyKey);
    if ($cached) {
      return array_merge($cached, ['idempotent' => true]);
    }

    // ── Score ─────────────────────────────────────────────────────────────
    $scoringResult = $this->scorer->scoreTransaction($data);

    // ── Persist ───────────────────────────────────────────────────────────
    $transaction = $this->transactionService->createFromIntercept(
      $merchant,
      $data,
      $scoringResult
    );

    // ── Evidence bundle ───────────────────────────────────────────────────
    $evidenceBundleId = null;
    if ($scoringResult->decision !== DecisionType::Decline) {
      try {
        $bundle           = $this->createEvidenceBundle->execute($transaction, $merchant);
        $evidenceBundleId = $bundle->ulid;
      } catch (\Exception $e) {
        Log::error('Evidence bundle creation failed', [
          'transaction_id' => $transaction->ulid,
          'error'          => $e->getMessage(),
        ]);
      }
    }

    // ── Fire webhooks ─────────────────────────────────────────────────────
    if ($scoringResult->decision === DecisionType::Decline) {
      $this->webhookDispatcher->transactionDeclined($transaction, $merchant);
    } else {
      $this->webhookDispatcher->transactionScored($transaction, $merchant);
    }

    // ── Build response ────────────────────────────────────────────────────
    $response = [
      'transaction_id'     => $transaction->ulid,
      'decision'           => $scoringResult->decision->value,
      'risk_score'         => $scoringResult->score,
      'risk_level'         => $scoringResult->riskLevel->value,
      'status'             => $transaction->status->value,
      'currency'           => $transaction->currency,
      'amount'             => $transaction->amount,
      'evidence_bundle_id' => $evidenceBundleId,
      'evidence_pending'   => $evidenceBundleId === null
        && $scoringResult->decision !== DecisionType::Decline,
      'signals'            => $scoringResult->signals,
      'processed_at'       => $transaction->created_at->toIso8601String(),
      'idempotent'         => false,
    ];

    // ── Idempotency cache ─────────────────────────────────────────────────
    $this->idempotency->store((string) $merchant->id, $idempotencyKey, $response);

    // ── Audit ─────────────────────────────────────────────────────────────
    AuditLog::create([
      'merchant_id'   => $merchant->id,
      'actor_type'    => ActorType::Merchant->value,
      'action'        => 'transaction.intercepted',
      'resource_type' => 'transaction',
      'resource_id'   => $transaction->ulid,
      'after_state'   => [
        'decision'           => $scoringResult->decision->value,
        'risk_score'         => $scoringResult->score,
        'status'             => $transaction->status->value,
        'evidence_bundle_id' => $evidenceBundleId,
      ],
      'ip_address'    => request()->ip(),
      'created_at'    => now(),
    ]);

    return $response;
  }
}
