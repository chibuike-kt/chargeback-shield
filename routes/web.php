<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DisputeController;
use Illuminate\Support\Facades\Route;

// ── Guest routes ──────────────────────────────────────────────────────────────
Route::middleware('guest:merchant')->group(function () {
    Route::get('/',        [AuthController::class, 'showLogin'])->name('home');
    Route::get('/login',   [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',  [AuthController::class, 'login']);
    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// ── Authenticated routes ───────────────────────────────────────────────────────
Route::middleware('auth:merchant')->group(function () {
    Route::get('/dashboard',      [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/transactions',   fn() => view('coming-soon', ['title' => 'Transactions']))->name('transactions');
    Route::get('/disputes',       [DisputeController::class, 'index'])->name('disputes');
    Route::get('/disputes/{ulid}', [DisputeController::class, 'show'])->name('disputes.show');
    Route::get('/disputes/{ulid}/pdf', [DisputeController::class, 'downloadPdf'])->name('disputes.pdf');
    Route::get('/webhooks',       fn() => view('coming-soon', ['title' => 'Webhooks']))->name('webhooks');
    Route::get('/trust-registry', fn() => view('coming-soon', ['title' => 'Trust Registry']))->name('trust-registry');
    Route::get('/simulate',       fn() => view('coming-soon', ['title' => 'Simulation Panel']))->name('simulate');
    Route::get('/audit-log',      fn() => view('coming-soon', ['title' => 'Audit Log']))->name('audit-log');
    Route::get('/settings',       fn() => view('coming-soon', ['title' => 'Settings']))->name('settings');
    Route::post('/logout',        [AuthController::class, 'logout'])->name('logout');
});
