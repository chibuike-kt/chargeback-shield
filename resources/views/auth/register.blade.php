@extends('layouts.auth')
@section('title', 'Create Account')

@section('content')
<div class="mb-6">
  <h2 class="text-xl font-bold text-slate-800">Create your account</h2>
  <p class="text-sm text-slate-500 mt-1">Start protecting transactions in minutes</p>
</div>

<form method="POST" action="{{ route('register') }}" class="space-y-4">
  @csrf

  <div>
    <label for="company_name" class="block text-sm font-medium text-slate-700 mb-1">Company name</label>
    <input
      id="company_name" name="company_name" type="text"
      value="{{ old('company_name') }}"
      class="input-field @error('company_name') border-red-400 @enderror"
      placeholder="Acme Fintech Ltd">
    @error('company_name')
    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
  </div>

  <div>
    <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email address</label>
    <input
      id="email" name="email" type="email"
      value="{{ old('email') }}"
      class="input-field @error('email') border-red-400 @enderror"
      placeholder="you@company.com">
    @error('email')
    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
  </div>

  <div>
    <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
    <input
      id="password" name="password" type="password"
      class="input-field @error('password') border-red-400 @enderror"
      placeholder="Min 8 characters">
    @error('password')
    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
  </div>

  <div>
    <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">Confirm password</label>
    <input
      id="password_confirmation" name="password_confirmation" type="password"
      class="input-field"
      placeholder="Repeat password">
  </div>

  <button type="submit" class="btn-primary w-full mt-2">
    Create account &amp; get API key
  </button>
</form>

<p class="mt-5 text-center text-sm text-slate-500">
  Already have an account?
  <a href="{{ route('login') }}" class="font-medium text-brand-600 hover:text-brand-500">Sign in</a>
</p>
@endsection
