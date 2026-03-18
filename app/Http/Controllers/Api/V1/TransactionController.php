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
    private InterceptTransaction $interceptAction,
    private EvidenceService      $evidenceService,
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
