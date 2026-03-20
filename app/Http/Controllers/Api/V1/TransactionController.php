<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Transactions\InterceptTransaction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TransactionInterceptRequest;
use App\Http\Resources\Api\ApiResponse;
use App\Models\Transaction;
use App\Services\EvidenceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
  public function __construct(
    private InterceptTransaction  $interceptAction,
    private EvidenceService       $evidenceService,
    private \App\Services\ScoringService     $scoringService,
    private \App\Services\TransactionService $transactionService,
    private \App\Actions\Transactions\CreateEvidenceBundle $createEvidenceBundle,
    private \App\Services\IdempotencyService $idempotencyService,
    private \App\Services\WebhookDispatcher  $webhookDispatcher,
  ) {}

  /**
   * POST /api/v1/transaction/intercept
   */
  public function intercept(TransactionInterceptRequest $request): JsonResponse
  {
    $merchant = $request->attributes->get('merchant');
    $result   = $this->interceptAction->execute($merchant, $request->validated());

    $message = match ($result['decision']) {
      'allow'   => 'Transaction approved.',
      'step_up' => 'Transaction approved with step-up authentication required.',
      'decline' => 'Transaction declined due to high risk score.',
      default   => 'Transaction processed.',
    };

    if ($result['idempotent']) {
      $message = 'Idempotent response — transaction already processed.';
    }

    return ApiResponse::success($result, $message);
  }

  /**
 * POST /api/v1/transaction/score
 *
 * Post-authorization scoring endpoint.
 * Call this AFTER approving the transaction — fire and forget.
 * Scores the transaction, locks evidence, fires retroactive
 * webhook if risk score is high. Never blocks the payment flow.
 */
public function score(Request $request): JsonResponse
{
    $merchant = $request->attributes->get('merchant');

    $validated = $request->validate([
        'idempotency_key'     => ['required', 'string', 'min:8', 'max:128'],
        'card_bin'            => ['required', 'string', 'size:6'],
        'card_last4'          => ['required', 'string', 'size:4'],
        'card_country'        => ['nullable', 'string', 'size:2'],
        'amount'              => ['required', 'integer', 'min:1'],
        'currency'            => ['required', 'string', 'size:3'],
        'ip_address'          => ['nullable', 'ip'],
        'ip_country'          => ['nullable', 'string', 'size:2'],
        'ip_city'             => ['nullable', 'string', 'max:100'],
        'device_fingerprint'  => ['nullable', 'string', 'max:128'],
        'session_token'       => ['nullable', 'string', 'max:128'],
        'session_age_seconds' => ['nullable', 'integer', 'min:0'],
        'merchant_category'   => ['nullable', 'string', 'max:10'],
        // Post-auth specific — the fintech's own transaction reference
        'external_reference'  => ['nullable', 'string', 'max:128'],
    ]);

    // Check idempotency — don't double-score the same transaction
    $cached = $this->idempotencyService->get(
        (string) $merchant->id,
        $validated['idempotency_key']
    );

    if ($cached) {
        return ApiResponse::success(
            array_merge($cached, ['idempotent' => true]),
            'Already scored — returning cached result.'
        );
    }

    // Score the transaction
    $scoringResult = $this->scoringService->scoreTransaction($validated);

    // Determine status — post-auth means already approved by fintech
    // We record it as approved regardless of our score
    $transaction = $this->transactionService->createPostAuth(
        $merchant,
        $validated,
        $scoringResult
    );

    // Lock evidence for all post-auth transactions
    // regardless of risk score — we always want the proof
    $evidenceBundleId = null;
    try {
        $bundle           = $this->createEvidenceBundle->execute($transaction, $merchant);
        $evidenceBundleId = $bundle->ulid;
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Post-auth evidence bundle failed', [
            'transaction_id' => $transaction->ulid,
            'error'          => $e->getMessage(),
        ]);
    }

    // Fire retroactive high-risk webhook if score is concerning
    $this->webhookDispatcher->transactionScored($transaction, $merchant);

    if ($scoringResult->score >= 0.70) {
        $this->webhookDispatcher->highRiskDetected($transaction, $merchant);
    }

    // Audit
    \App\Models\AuditLog::create([
        'merchant_id'   => $merchant->id,
        'actor_type'    => \App\Enums\ActorType::Merchant->value,
        'action'        => 'transaction.scored_post_auth',
        'resource_type' => 'transaction',
        'resource_id'   => $transaction->ulid,
        'after_state'   => [
            'risk_score'         => $scoringResult->score,
            'risk_level'         => $scoringResult->riskLevel->value,
            'evidence_bundle_id' => $evidenceBundleId,
        ],
        'ip_address'    => $request->ip(),
        'created_at'    => now(),
    ]);

    $response = [
        'transaction_id'     => $transaction->ulid,
        'risk_score'         => $scoringResult->score,
        'risk_level'         => $scoringResult->riskLevel->value,
        'evidence_bundle_id' => $evidenceBundleId,
        'high_risk_detected' => $scoringResult->score >= 0.70,
        'signals'            => $scoringResult->signals,
        'scored_at'          => $transaction->created_at->toIso8601String(),
        'idempotent'         => false,
    ];

    $this->idempotencyService->store(
        (string) $merchant->id,
        $validated['idempotency_key'],
        $response
    );

    return ApiResponse::success(
        $response,
        'Transaction scored and evidence locked.'
    );
}

  /**
   * GET /api/v1/transaction/{ulid}
   */
  public function show(Request $request, string $ulid): JsonResponse
  {
    $merchant    = $request->attributes->get('merchant');
    $transaction = Transaction::with(['riskSignalLogs', 'evidenceBundle'])
      ->where('ulid', $ulid)
      ->where('merchant_id', $merchant->id)
      ->first();

    if (!$transaction) {
      return ApiResponse::error('Transaction not found.', 404);
    }

    return ApiResponse::success([
      'transaction_id'    => $transaction->ulid,
      'decision'          => $transaction->decision->value,
      'risk_score'        => $transaction->risk_score,
      'risk_level'        => $transaction->risk_level->value,
      'status'            => $transaction->status->value,
      'amount'            => $transaction->amount,
      'currency'          => $transaction->currency,
      'card_bin'          => $transaction->card_bin,
      'card_last4'        => $transaction->card_last4,
      'card_country'      => $transaction->card_country,
      'ip_address'        => $transaction->ip_address,
      'ip_country'        => $transaction->ip_country,
      'merchant_category' => $transaction->merchant_category,
      'has_evidence'      => $transaction->hasEvidence(),
      'evidence_bundle_id' => $transaction->evidenceBundle?->ulid,
      'signals'           => $transaction->riskSignalLogs->map(fn($s) => [
        'signal_name'           => $s->signal_name,
        'raw_value'             => $s->raw_value,
        'normalized_score'      => $s->normalized_score,
        'weight'                => $s->weight,
        'weighted_contribution' => $s->weighted_contribution,
      ]),
      'processed_at'      => $transaction->created_at->toIso8601String(),
    ]);
  }

  /**
   * GET /api/v1/transaction/{ulid}/evidence
   */
  public function evidence(Request $request, string $ulid): JsonResponse
  {
    $merchant    = $request->attributes->get('merchant');
    $transaction = Transaction::with('evidenceBundle')
      ->where('ulid', $ulid)
      ->where('merchant_id', $merchant->id)
      ->first();

    if (!$transaction) {
      return ApiResponse::error('Transaction not found.', 404);
    }

    if (!$transaction->evidenceBundle) {
      return ApiResponse::error(
        'No evidence bundle found for this transaction. Declined transactions do not generate evidence.',
        404
      );
    }

    try {
      $result = $this->evidenceService->retrieveBundle(
        $transaction->evidenceBundle,
        $merchant
      );

      return ApiResponse::success($result, 'Evidence bundle retrieved and verified.');
    } catch (\Exception $e) {
      return ApiResponse::error(
        'Evidence bundle could not be decrypted: ' . $e->getMessage(),
        500
      );
    }
  }
}
