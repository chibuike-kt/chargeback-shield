<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DisputeController;
use App\Http\Controllers\SimulationController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:merchant')->group(function () {
    Route::get('/',          [AuthController::class, 'showLogin'])->name('home');
    Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [AuthController::class, 'login']);
    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth:merchant')->group(function () {
    Route::get('/dashboard',            [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/transactions',         fn() => view('coming-soon', ['title' => 'Transactions']))->name('transactions');
    Route::get('/disputes',             [DisputeController::class, 'index'])->name('disputes');
    Route::get('/disputes/{ulid}',      [DisputeController::class, 'show'])->name('disputes.show');
    Route::get('/disputes/{ulid}/pdf',  [DisputeController::class, 'downloadPdf'])->name('disputes.pdf');
    Route::get('/webhooks',             [WebhookController::class, 'index'])->name('webhooks');
    Route::post('/webhooks/{ulid}/retrigger', [WebhookController::class, 'retrigger'])->name('webhooks.retrigger');
    Route::get('/trust-registry',       fn() => view('coming-soon', ['title' => 'Trust Registry']))->name('trust-registry');
    Route::get('/simulate',             [SimulationController::class, 'index'])->name('simulate');
    Route::post('/simulate/run',        [SimulationController::class, 'run'])->name('simulate.run');
    Route::get('/audit-log',            [AuditLogController::class, 'index'])->name('audit-log');
    Route::get('/settings',             fn() => view('coming-soon', ['title' => 'Settings']))->name('settings');
    Route::post('/logout',              [AuthController::class, 'logout'])->name('logout');
});
