<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Merchants\GenerateMerchantCredentials;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Merchant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AuthController extends Controller
{
  public function __construct(
    private GenerateMerchantCredentials $credentials
  ) {}

  // ── Registration ──────────────────────────────────────────────────────────

  public function showRegister(): View
  {
    return view('auth.register');
  }

  public function register(RegisterRequest $request): RedirectResponse
  {
    $merchant = DB::transaction(function () use ($request) {
      return Merchant::create([
        'company_name'   => $request->company_name,
        'email'          => $request->email,
        'password'       => $request->password,
        'api_key'        => $this->credentials->generateApiKey(),
        'webhook_secret' => $this->credentials->generateWebhookSecret(),
      ]);
    });

    Auth::guard('merchant')->login($merchant);

    session()->flash('success', 'Welcome to Chargeback Shield. Your API credentials are ready.');

    return redirect()->route('dashboard');
  }

  // ── Login ─────────────────────────────────────────────────────────────────

  public function showLogin(): View
  {
    return view('auth.login');
  }

  public function login(LoginRequest $request): RedirectResponse
  {
    $credentials = $request->only('email', 'password');

    if (!Auth::guard('merchant')->attempt($credentials, $request->boolean('remember'))) {
      return back()->withErrors([
        'email' => 'These credentials do not match our records.',
      ])->onlyInput('email');
    }

    $request->session()->regenerate();

    return redirect()->intended(route('dashboard'));
  }

  // ── Logout ────────────────────────────────────────────────────────────────

  public function logout(Request $request): RedirectResponse
  {
    Auth::guard('merchant')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
  }
}
