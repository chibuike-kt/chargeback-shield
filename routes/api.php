<?php

use App\Http\Controllers\Api\V1\TransactionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('merchant.api')->group(function () {

  // ── Transactions ──────────────────────────────────────────────────────────
  Route::post('transaction/intercept', [TransactionController::class, 'intercept']);
  Route::get('transaction/{ulid}',     [TransactionController::class, 'show']);
});
