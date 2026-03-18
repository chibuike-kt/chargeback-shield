<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FileDisputeRequest;
use App\Http\Resources\Api\ApiResponse;
use App\Models\Dispute;
use App\Models\Transaction;
use App\Services\DisputeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DisputeController extends Controller
{
  public function __construct(
    private DisputeService $disputeService,
  ) {}

  /**
   * POST /api/v1/dispute
   * File a chargeback and immediately generate response.
   */
  public function file(FileDisputeRequest $request): JsonResponse
  {
    $merchant = $request->attributes->get('merchant');

    // Find the transaction
    $transaction = Transaction::with('evidenceBundle')
      ->where('ulid', $request->transaction_id)
      ->where('merchant_id', $merchant->id)
      ->first();

    if (!$transaction) {
      return ApiResponse::error('Transaction not found.', 404);
    }

    if (!$transaction->evidenceBundle) {
      return ApiResponse::error(
        'No evidence bundle found for this transaction. ' .
          'Cannot generate dispute response without locked evidence.',
        422
      );
    }

    // Check if dispute already exists
    if ($transaction->dispute) {
      return ApiResponse::error(
        'A dispute has already been filed for this transaction.',
        409
      );
    }

    $dispute = $this->disputeService->fileDispute(
      $merchant,
      $transaction,
      $request->validated()
    );

    return ApiResponse::success([
      'dispute_id'          => $dispute->ulid,
      'status'              => $dispute->status->value,
      'reason_code'         => $dispute->reason_code,
      'reason_description'  => $dispute->reason_description,
      'network'             => $dispute->network->value,
      'response_ready'      => true,
      'responded_at'        => $dispute->responded_at?->toIso8601String(),
      'response_document'   => $dispute->response_document,
    ], 'Dispute filed and response document generated successfully.', 201);
  }

  /**
   * GET /api/v1/dispute/{ulid}
   */
  public function show(Request $request, string $ulid): JsonResponse
  {
    $merchant = $request->attributes->get('merchant');
    $dispute  = Dispute::with(['transaction', 'transaction.evidenceBundle'])
      ->where('ulid', $ulid)
      ->where('merchant_id', $merchant->id)
      ->first();

    if (!$dispute) {
      return ApiResponse::error('Dispute not found.', 404);
    }

    return ApiResponse::success($this->formatDispute($dispute));
  }

  /**
   * GET /api/v1/dispute/{ulid}/response
   * Returns the ready-to-submit response document.
   */
  public function response(Request $request, string $ulid): JsonResponse
  {
    $merchant = $request->attributes->get('merchant');
    $dispute  = Dispute::with(['transaction', 'transaction.evidenceBundle'])
      ->where('ulid', $ulid)
      ->where('merchant_id', $merchant->id)
      ->first();

    if (!$dispute) {
      return ApiResponse::error('Dispute not found.', 404);
    }

    if (!$dispute->response_document) {
      return ApiResponse::error('Response document not yet generated.', 404);
    }

    return ApiResponse::success([
      'dispute_id'        => $dispute->ulid,
      'status'            => $dispute->status->value,
      'response_document' => $dispute->response_document,
      'responded_at'      => $dispute->responded_at?->toIso8601String(),
    ], 'Dispute response document retrieved.');
  }

  /**
   * GET /api/v1/disputes
   */
  public function index(Request $request): JsonResponse
  {
    $merchant  = $request->attributes->get('merchant');
    $disputes  = Dispute::with('transaction')
      ->where('merchant_id', $merchant->id)
      ->latest()
      ->paginate(20);

    return ApiResponse::success(
      $disputes->through(fn($d) => $this->formatDispute($d)),
      'Disputes retrieved.'
    );
  }

  private function formatDispute(Dispute $dispute): array
  {
    return [
      'dispute_id'         => $dispute->ulid,
      'transaction_id'     => $dispute->transaction->ulid,
      'reason_code'        => $dispute->reason_code,
      'reason_description' => $dispute->reason_description,
      'network'            => $dispute->network->value,
      'status'             => $dispute->status->value,
      'amount'             => $dispute->transaction->amount,
      'currency'           => $dispute->transaction->currency,
      'response_ready'     => !is_null($dispute->response_document),
      'filed_at'           => $dispute->filed_at?->toIso8601String(),
      'responded_at'       => $dispute->responded_at?->toIso8601String(),
      'resolved_at'        => $dispute->resolved_at?->toIso8601String(),
      'created_at'         => $dispute->created_at->toIso8601String(),
    ];
  }
}
