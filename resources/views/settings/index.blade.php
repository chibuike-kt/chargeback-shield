@extends('layouts.app')
@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

  {{-- Profile --}}
  <div class="card overflow-hidden" id="profile">
    <div class="px-6 py-4 border-b border-slate-100">
      <h3 class="text-sm font-semibold text-slate-800">Profile</h3>
      <p class="text-xs text-slate-400 mt-0.5">Update your company name and email address</p>
    </div>
    <form method="POST" action="{{ route('settings.profile') }}" class="px-6 py-5 space-y-4">
      @csrf
      @method('PATCH')

      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Company name</label>
        <input type="text" name="company_name"
          value="{{ old('company_name', $merchant->company_name) }}"
          class="input-field @error('company_name') border-red-400 @enderror">
        @error('company_name')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
      </div>

      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Email address</label>
        <input type="email" name="email"
          value="{{ old('email', $merchant->email) }}"
          class="input-field @error('email') border-red-400 @enderror">
        @error('email')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
      </div>

      <div class="flex justify-end">
        <button type="submit" class="btn-primary">Save changes</button>
      </div>
    </form>
  </div>

  {{-- Password --}}
  <div class="card overflow-hidden" id="password">
    <div class="px-6 py-4 border-b border-slate-100">
      <h3 class="text-sm font-semibold text-slate-800">Password</h3>
      <p class="text-xs text-slate-400 mt-0.5">Change your account password</p>
    </div>
    <form method="POST" action="{{ route('settings.password') }}" class="px-6 py-5 space-y-4">
      @csrf
      @method('PATCH')

      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Current password</label>
        <input type="password" name="current_password"
          class="input-field @error('current_password') border-red-400 @enderror">
        @error('current_password')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
      </div>

      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">New password</label>
        <input type="password" name="password"
          class="input-field @error('password') border-red-400 @enderror"
          placeholder="Min 8 characters">
        @error('password')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
      </div>

      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Confirm new password</label>
        <input type="password" name="password_confirmation" class="input-field">
      </div>

      <div class="flex justify-end">
        <button type="submit" class="btn-primary">Update password</button>
      </div>
    </form>
  </div>

  {{-- API Credentials --}}
  <div class="card overflow-hidden" id="credentials">
    <div class="px-6 py-4 border-b border-slate-100">
      <h3 class="text-sm font-semibold text-slate-800">API Credentials</h3>
      <p class="text-xs text-slate-400 mt-0.5">Your API key and webhook secret</p>
    </div>
    <div class="px-6 py-5 space-y-5">

      {{-- API Key --}}
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-2">API Key</label>
        <div class="flex items-center gap-2"
          x-data="{ show: false, copied: false }">
          <div class="flex-1 flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2">
            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
            </svg>
            <span class="text-xs font-mono text-slate-600 flex-1 truncate"
              x-text="show ? '{{ $merchant->api_key }}' : '{{ substr($merchant->api_key, 0, 20) }}••••••••••••••••••••••••••'">
            </span>
          </div>
          <button @click="show = !show"
            class="btn-secondary text-xs py-2 px-3">
            <span x-text="show ? 'Hide' : 'Show'"></span>
          </button>
          <button
            @click="navigator.clipboard.writeText('{{ $merchant->api_key }}'); copied = true; setTimeout(() => copied = false, 2000)"
            class="btn-secondary text-xs py-2 px-3">
            <span x-text="copied ? 'Copied!' : 'Copy'"></span>
          </button>
        </div>
      </div>

      {{-- Webhook Secret --}}
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-2">Webhook Secret</label>
        <div class="flex items-center gap-2"
          x-data="{ show: false, copied: false }">
          <div class="flex-1 flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2">
            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
            <span class="text-xs font-mono text-slate-600 flex-1 truncate"
              x-text="show ? '{{ $merchant->webhook_secret }}' : '{{ substr($merchant->webhook_secret, 0, 20) }}••••••••••••••••••••••••••'">
            </span>
          </div>
          <button @click="show = !show"
            class="btn-secondary text-xs py-2 px-3">
            <span x-text="show ? 'Hide' : 'Show'"></span>
          </button>
          <button
            @click="navigator.clipboard.writeText('{{ $merchant->webhook_secret }}'); copied = true; setTimeout(() => copied = false, 2000)"
            class="btn-secondary text-xs py-2 px-3">
            <span x-text="copied ? 'Copied!' : 'Copy'"></span>
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- Webhook Configuration --}}
  <div class="card overflow-hidden" id="webhook">
    <div class="px-6 py-4 border-b border-slate-100">
      <h3 class="text-sm font-semibold text-slate-800">Webhook Configuration</h3>
      <p class="text-xs text-slate-400 mt-0.5">Where Chargeback Shield sends event notifications</p>
    </div>
    <div class="px-6 py-5 space-y-5">

      {{-- Webhook URL form --}}
      <form method="POST" action="{{ route('settings.webhook') }}" class="space-y-4">
        @csrf
        @method('PATCH')

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Webhook URL</label>
          <div class="flex gap-2">
            <input type="url" name="webhook_url"
              value="{{ old('webhook_url', $merchant->webhook_url) }}"
              placeholder="https://yourapp.com/webhooks/chargeback-shield"
              class="input-field @error('webhook_url') border-red-400 @enderror flex-1">
            <button type="submit" class="btn-primary shrink-0">Save</button>
          </div>
          @error('webhook_url')
          <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
          @enderror
        </div>
      </form>

      {{-- Test webhook --}}
      @if($merchant->webhook_url)
      <div class="pt-2 border-t border-slate-100"
        x-data="{ testing: false, result: null }">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-slate-700">Test endpoint</p>
            <p class="text-xs text-slate-400 mt-0.5">
              Send a test event to <span class="font-mono">{{ $merchant->webhook_url }}</span>
            </p>
          </div>
          <button
            @click="
                            testing = true;
                            result = null;
                            fetch('{{ route('settings.webhook.test') }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                    'Accept': 'application/json',
                                }
                            })
                            .then(r => r.json())
                            .then(d => { result = d; testing = false; })
                            .catch(() => { result = { success: false, message: 'Request failed' }; testing = false; })
                        "
            :disabled="testing"
            class="btn-secondary text-xs shrink-0">
            <span x-show="!testing">Send test event</span>
            <span x-show="testing" class="flex items-center gap-1.5">
              <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
              </svg>
              Testing...
            </span>
          </button>
        </div>

        <div x-show="result !== null" class="mt-3 px-4 py-3 rounded-lg text-sm"
          :class="result?.success ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700'">
          <div class="flex items-center gap-2">
            <svg x-show="result?.success" class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <svg x-show="!result?.success" class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
            <span x-text="result?.message"></span>
          </div>
        </div>
      </div>
      @endif
    </div>
  </div>

  {{-- Danger Zone --}}
  <div class="card overflow-hidden border-red-200" id="danger">
    <div class="px-6 py-4 border-b border-red-100" style="background:#fef2f2;">
      <h3 class="text-sm font-semibold text-red-700">Danger Zone</h3>
      <p class="text-xs text-red-400 mt-0.5">These actions are irreversible. Proceed with caution.</p>
    </div>
    <div class="px-6 py-5 space-y-5">

      {{-- Regenerate API key --}}
      <div class="flex items-start justify-between gap-6 pb-5 border-b border-slate-100"
        x-data="{ open: false }">
        <div>
          <p class="text-sm font-semibold text-slate-800">Regenerate API key</p>
          <p class="text-xs text-slate-500 mt-0.5">
            Your current API key will be immediately invalidated.
            Any integrations using the old key will stop working.
          </p>
        </div>
        <button @click="open = true"
          class="btn-secondary text-xs shrink-0 border-red-200 text-red-600 hover:bg-red-50">
          Regenerate
        </button>

        {{-- Confirmation modal --}}
        <div x-show="open" x-cloak
          class="fixed inset-0 z-50 flex items-center justify-center p-4"
          style="background: rgba(0,0,0,0.5);">
          <div class="bg-white rounded-2xl p-6 max-w-md w-full shadow-xl">
            <h4 class="text-base font-bold text-slate-900 mb-2">Regenerate API key?</h4>
            <p class="text-sm text-slate-500 mb-5">
              Your current key will stop working immediately.
              Type <strong class="text-slate-800">REGENERATE</strong> to confirm.
            </p>
            <form method="POST" action="{{ route('settings.api-key.regenerate') }}">
              @csrf
              <input type="text" name="confirm_regenerate_key"
                placeholder="Type REGENERATE"
                class="input-field mb-4"
                autocomplete="off">
              @error('confirm_regenerate_key')
              <p class="mb-3 text-xs text-red-600">{{ $message }}</p>
              @enderror
              <div class="flex gap-3">
                <button type="button" @click="open = false" class="btn-secondary flex-1">
                  Cancel
                </button>
                <button type="submit"
                  class="flex-1 py-2 px-4 rounded-lg text-sm font-semibold text-white bg-red-600 hover:bg-red-700 transition-colors">
                  Regenerate key
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      {{-- Regenerate webhook secret --}}
      <div class="flex items-start justify-between gap-6 pb-5 border-b border-slate-100"
        x-data="{ open: false }">
        <div>
          <p class="text-sm font-semibold text-slate-800">Regenerate webhook secret</p>
          <p class="text-xs text-slate-500 mt-0.5">
            Your current webhook secret will be invalidated.
            Update your webhook verification code immediately after.
          </p>
        </div>
        <button @click="open = true"
          class="btn-secondary text-xs shrink-0 border-red-200 text-red-600 hover:bg-red-50">
          Regenerate
        </button>

        <div x-show="open" x-cloak
          class="fixed inset-0 z-50 flex items-center justify-center p-4"
          style="background: rgba(0,0,0,0.5);">
          <div class="bg-white rounded-2xl p-6 max-w-md w-full shadow-xl">
            <h4 class="text-base font-bold text-slate-900 mb-2">Regenerate webhook secret?</h4>
            <p class="text-sm text-slate-500 mb-5">
              Your current webhook secret will stop working immediately.
              You will need to update your webhook verification code.
            </p>
            <form method="POST" action="{{ route('settings.webhook-secret.regenerate') }}">
              @csrf
              <div class="flex gap-3">
                <button type="button" @click="open = false" class="btn-secondary flex-1">
                  Cancel
                </button>
                <button type="submit"
                  class="flex-1 py-2 px-4 rounded-lg text-sm font-semibold text-white bg-red-600 hover:bg-red-700 transition-colors">
                  Regenerate secret
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      {{-- Delete account --}}
      <div class="flex items-start justify-between gap-6"
        x-data="{ open: false }">
        <div>
          <p class="text-sm font-semibold text-slate-800">Delete account</p>
          <p class="text-xs text-slate-500 mt-0.5">
            Permanently delete your account and all associated data.
            This cannot be undone.
          </p>
        </div>
        <button @click="open = true"
          class="btn-secondary text-xs shrink-0 border-red-200 text-red-600 hover:bg-red-50">
          Delete account
        </button>

        <div x-show="open" x-cloak
          class="fixed inset-0 z-50 flex items-center justify-center p-4"
          style="background: rgba(0,0,0,0.5);">
          <div class="bg-white rounded-2xl p-6 max-w-md w-full shadow-xl">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-4"
              style="background:#fef2f2;">
              <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
            </div>
            <h4 class="text-base font-bold text-slate-900 mb-2">Delete your account?</h4>
            <p class="text-sm text-slate-500 mb-5">
              All your transactions, evidence bundles, disputes, and webhook logs
              will be permanently deleted. This cannot be undone.
              Type your email address to confirm.
            </p>
            <form method="POST" action="{{ route('settings.delete') }}">
              @csrf
              @method('DELETE')
              <input type="email" name="confirm_email"
                placeholder="{{ $merchant->email }}"
                class="input-field mb-4">
              <div class="flex gap-3">
                <button type="button" @click="open = false" class="btn-secondary flex-1">
                  Cancel
                </button>
                <button type="submit"
                  class="flex-1 py-2 px-4 rounded-lg text-sm font-semibold text-white bg-red-600 hover:bg-red-700 transition-colors">
                  Delete everything
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

    </div>
  </div>

</div>
@endsection
