<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DisputeController;
use App\Http\Controllers\SimulationController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TrustRegistryController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

// ── Landing page ──────────────────────────────────────────────────────────────
Route::get('/', fn() => view('landing.index'))->name('home');
Route::get('/docs', fn() => view('docs.index'))->name('docs');

// ── Auth routes under /app ────────────────────────────────────────────────────
Route::prefix('app')->group(function () {

    Route::middleware('guest:merchant')->group(function () {
        Route::get('/',          [AuthController::class, 'showLogin'])->name('home');
        Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login',    [AuthController::class, 'login'])->middleware('throttle:10,1');
        Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
    });

    Route::middleware('auth:merchant')->group(function () {
        Route::get('/dashboard',                [DashboardController::class,     'index'])->name('dashboard');
        Route::get('/transactions',             [TransactionController::class,   'index'])->name('transactions');
        Route::get('/transactions/{ulid}',      [TransactionController::class,   'show'])->name('transactions.show');
        Route::get('/disputes',                 [DisputeController::class,       'index'])->name('disputes');
        Route::get('/disputes/{ulid}',          [DisputeController::class,       'show'])->name('disputes.show');
        Route::get('/disputes/{ulid}/pdf',      [DisputeController::class,       'downloadPdf'])->name('disputes.pdf');
        Route::get('/webhooks',                 [WebhookController::class,       'index'])->name('webhooks');
        Route::post('/webhooks/{ulid}/retrigger',[WebhookController::class,      'retrigger'])->name('webhooks.retrigger');
        Route::get('/trust-registry',           [TrustRegistryController::class, 'index'])->name('trust-registry');
        Route::get('/simulate',                 [SimulationController::class,    'index'])->name('simulate');
        Route::post('/simulate/run',            [SimulationController::class,    'run'])->name('simulate.run');
        Route::get('/audit-log',                [AuditLogController::class,      'index'])->name('audit-log');
        Route::get('/settings',                          [SettingsController::class, 'index'])->name('settings');
        Route::patch('/settings/profile',               [SettingsController::class, 'updateProfile'])->name('settings.profile');
        Route::patch('/settings/password',              [SettingsController::class, 'updatePassword'])->name('settings.password');
        Route::patch('/settings/webhook',               [SettingsController::class, 'updateWebhook'])->name('settings.webhook');
        Route::post('/settings/webhook/test',           [SettingsController::class, 'testWebhook'])->name('settings.webhook.test');
        Route::post('/settings/api-key/regenerate',     [SettingsController::class, 'regenerateApiKey'])->name('settings.api-key.regenerate');
        Route::post('/settings/webhook-secret/regenerate', [SettingsController::class, 'regenerateWebhookSecret'])->name('settings.webhook-secret.regenerate');
        Route::delete('/settings',                      [SettingsController::class, 'deleteAccount'])->name('settings.delete');

        Route::post('/logout',                  [AuthController::class,          'logout'])->name('logout');
    });
});

