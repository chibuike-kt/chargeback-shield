<?php

use App\Http\Controllers\Api\V1\DisputeController;
use App\Http\Controllers\Api\V1\TransactionController;
use Illuminate\Support\Facades\Route;

// Debug route — no auth or rate limiting
Route::get('/debug-headers', function (Illuminate\Http\Request $request) {
  return response()->json([
    'x_api_key_header' => $request->header('X-API-Key'),
    'all_headers'      => $request->headers->all(),
  ]);
});

Route::prefix('v1')
  ->middleware(['merchant.api', 'log.api'])
  ->group(function () {

    // ── Transactions ──────────────────────────────────────────────────────
    // Pre-auth — synchronous, fintech waits for decision
    Route::post(
      'transaction/intercept',
      [TransactionController::class, 'intercept']
    )->middleware('rate.limit:transaction_intercept');

    // Post-auth — async-friendly, fintech already approved
    Route::post(
      'transaction/score',
      [TransactionController::class, 'score']
    )->middleware('rate.limit:transaction_intercept');

  Route::get('/transactions/export', [TransactionController::class, 'export'])->name('transactions.export');

    Route::get(
      'transaction/{ulid}',
      [TransactionController::class, 'show']
    )->middleware('rate.limit:standard_api');

    Route::get(
      'transaction/{ulid}/evidence',
      [TransactionController::class, 'evidence']
    )->middleware('rate.limit:standard_api');

    // ── Disputes ──────────────────────────────────────────────────────────
    Route::post(
      'dispute',
      [DisputeController::class, 'file']
    )->middleware('rate.limit:standard_api');

    Route::get(
      'disputes',
      [DisputeController::class, 'index']
    )->middleware('rate.limit:standard_api');

    Route::get(
      'dispute/{ulid}',
      [DisputeController::class, 'show']
    )->middleware('rate.limit:standard_api');

    Route::get(
      'dispute/{ulid}/response',
      [DisputeController::class, 'response']
    )->middleware('rate.limit:standard_api');

  });
