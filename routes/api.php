<?php

use App\Http\Controllers\Api\V1\DisputeController;
use App\Http\Controllers\Api\V1\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/debug-headers', function (Illuminate\Http\Request $request) {
  return response()->json([
    'x_api_key_header' => $request->header('X-API-Key'),
    'all_headers'      => $request->headers->all(),
  ]);
});

Route::prefix('v1')->middleware('merchant.api')->group(function () {

  // ── Transactions ──────────────────────────────────────────────────────────
  Route::post('transaction/intercept',      [TransactionController::class, 'intercept']);
  Route::get('transaction/{ulid}',          [TransactionController::class, 'show']);
  Route::get('transaction/{ulid}/evidence', [TransactionController::class, 'evidence']);

  // ── Disputes ──────────────────────────────────────────────────────────────
  Route::post('dispute',              [DisputeController::class, 'file']);
  Route::get('disputes',              [DisputeController::class, 'index']);
  Route::get('dispute/{ulid}',        [DisputeController::class, 'show']);
  Route::get('dispute/{ulid}/response', [DisputeController::class, 'response']);
});
