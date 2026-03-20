<?php

namespace App\Http\Controllers;

use App\Actions\Merchants\GenerateMerchantCredentials;
use App\Enums\ActorType;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class SettingsController extends Controller
{
  public function __construct(
    private GenerateMerchantCredentials $credentials,
  ) {}

  public function index(): View
  {
    return view('settings.index', [
      'merchant' => auth('merchant')->user(),
    ]);
  }

  // ── Update profile ────────────────────────────────────────────────────────

  public function updateProfile(Request $request): RedirectResponse
  {
    $merchant = auth('merchant')->user();

    $validated = $request->validate([
      'company_name' => ['required', 'string', 'min:2', 'max:100'],
      'email'        => ['required', 'email', 'unique:merchants,email,' . $merchant->id],
    ]);

    $before = [
      'company_name' => $merchant->company_name,
      'email'        => $merchant->email,
    ];

    $merchant->update($validated);

    AuditLog::create([
      'merchant_id'   => $merchant->id,
      'actor_type'    => ActorType::Merchant->value,
      'action'        => 'merchant.profile_updated',
      'resource_type' => 'merchant',
      'resource_id'   => $merchant->ulid,
      'before_state'  => $before,
      'after_state'   => $validated,
      'ip_address'    => $request->ip(),
      'created_at'    => now(),
    ]);

    return back()->with('success', 'Profile updated successfully.');
  }

  
  public function deleteAccount(Request $request): RedirectResponse
{
    $merchant = auth('merchant')->user();

    $request->validate([
        'confirm_email' => ['required', 'email'],
    ]);

    if ($request->confirm_email !== $merchant->email) {
        return back()->withErrors([
            'confirm_email' => 'Email address does not match.',
        ])->withFragment('danger');
    }

    auth('merchant')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    $merchant->delete();

    return redirect()->route('home')
        ->with('success', 'Your account has been deleted.');
}

  // ── Update password ───────────────────────────────────────────────────────

  public function updatePassword(Request $request): RedirectResponse
  {
    $merchant = auth('merchant')->user();

    $request->validate([
      'current_password' => ['required', 'string'],
      'password'         => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    if (!Hash::check($request->current_password, $merchant->password)) {
      return back()->withErrors([
        'current_password' => 'Current password is incorrect.',
      ])->withFragment('password');
    }

    $merchant->update([
      'password' => Hash::make($request->password),
    ]);

    AuditLog::create([
      'merchant_id'   => $merchant->id,
      'actor_type'    => ActorType::Merchant->value,
      'action'        => 'merchant.password_changed',
      'resource_type' => 'merchant',
      'resource_id'   => $merchant->ulid,
      'before_state'  => null,
      'after_state'   => ['password' => 'changed'],
      'ip_address'    => $request->ip(),
      'created_at'    => now(),
    ]);

    return back()->with('success', 'Password updated successfully.');
  }

  // ── Regenerate API key ────────────────────────────────────────────────────

  public function regenerateApiKey(Request $request): RedirectResponse
  {
    $merchant = auth('merchant')->user();

    $request->validate([
      'confirm_regenerate_key' => ['required', 'in:REGENERATE'],
    ], [
      'confirm_regenerate_key.in' => 'Type REGENERATE to confirm.',
    ]);

    $oldKey = $merchant->api_key;
    $newKey = $this->credentials->generateApiKey();

    $merchant->update(['api_key' => $newKey]);

    AuditLog::create([
      'merchant_id'   => $merchant->id,
      'actor_type'    => ActorType::Merchant->value,
      'action'        => 'merchant.api_key_regenerated',
      'resource_type' => 'merchant',
      'resource_id'   => $merchant->ulid,
      'before_state'  => ['api_key' => substr($oldKey, 0, 16) . '...'],
      'after_state'   => ['api_key' => substr($newKey, 0, 16) . '...'],
      'ip_address'    => $request->ip(),
      'created_at'    => now(),
    ]);

    return back()->with('success', 'API key regenerated. Update your integration immediately — your old key is now invalid.');
  }

  // ── Regenerate webhook secret ─────────────────────────────────────────────

  public function regenerateWebhookSecret(Request $request): RedirectResponse
  {
    $merchant = auth('merchant')->user();

    $newSecret = $this->credentials->generateWebhookSecret();
    $merchant->update(['webhook_secret' => $newSecret]);

    AuditLog::create([
      'merchant_id'   => $merchant->id,
      'actor_type'    => ActorType::Merchant->value,
      'action'        => 'merchant.webhook_secret_regenerated',
      'resource_type' => 'merchant',
      'resource_id'   => $merchant->ulid,
      'before_state'  => null,
      'after_state'   => ['webhook_secret' => 'regenerated'],
      'ip_address'    => $request->ip(),
      'created_at'    => now(),
    ]);

    return back()->with('success', 'Webhook secret regenerated. Update your webhook verification code immediately.');
  }

  // ── Update webhook URL ────────────────────────────────────────────────────

  public function updateWebhook(Request $request): RedirectResponse
  {
    $merchant = auth('merchant')->user();

    $validated = $request->validate([
      'webhook_url' => ['nullable', 'url', 'max:500'],
    ]);

    $merchant->update($validated);

    AuditLog::create([
      'merchant_id'   => $merchant->id,
      'actor_type'    => ActorType::Merchant->value,
      'action'        => 'merchant.webhook_url_updated',
      'resource_type' => 'merchant',
      'resource_id'   => $merchant->ulid,
      'before_state'  => ['webhook_url' => $merchant->webhook_url],
      'after_state'   => $validated,
      'ip_address'    => $request->ip(),
      'created_at'    => now(),
    ]);

    return back()->with('success', 'Webhook URL updated.');
  }

  // ── Test webhook endpoint ─────────────────────────────────────────────────

  public function testWebhook(Request $request)
  {
    $merchant = auth('merchant')->user();

    if (!$merchant->webhook_url) {
      return response()->json([
        'success' => false,
        'message' => 'No webhook URL configured.',
      ]);
    }

    $payload = [
      'event'     => 'webhook.test',
      'merchant'  => $merchant->ulid,
      'message'   => 'This is a test webhook from Chargeback Shield.',
      'timestamp' => now()->toIso8601String(),
    ];

    $signature = 'sha256=' . hash_hmac(
      'sha256',
      json_encode($payload),
      $merchant->webhook_secret
    );

    try {
      $response = Http::timeout(5)
        ->withHeaders([
          'Content-Type'            => 'application/json',
          'X-Chargeback-Shield-Sig' => $signature,
          'X-Event-Type'            => 'webhook.test',
          'User-Agent'              => 'ChargebackShield/1.0',
        ])
        ->post($merchant->webhook_url, $payload);

      return response()->json([
        'success'     => $response->successful(),
        'http_status' => $response->status(),
        'message'     => $response->successful()
          ? "Webhook delivered successfully — HTTP {$response->status()}"
          : "Webhook failed — HTTP {$response->status()}",
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Could not reach endpoint: ' . $e->getMessage(),
      ]);
    }
  }
}
