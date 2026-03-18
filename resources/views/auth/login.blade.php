@extends('layouts.auth')
@section('title', 'Sign In')

@section('content')
<div class="mb-6">
  <h2 class="text-xl font-bold text-slate-800">Welcome back</h2>
  <p class="text-sm text-slate-500 mt-1">Sign in to your merchant dashboard</p>
</div>

@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm">
  {{ session('success') }}
</div>
@endif

<form method="POST" action="{{ route('login') }}" class="space-y-4">
  @csrf

  <div>
    <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email address</label>
    <input
      id="email" name="email" type="email" autocomplete="email"
      value="{{ old('email') }}"
      class="input-field @error('email') border-red-400 @enderror"
      placeholder="you@company.com">
    @error('email')
    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
  </div>

  <div>
    <div class="flex items-center justify-between mb-1">
      <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
    </div>
    <input
      id="password" name="password" type="password" autocomplete="current-password"
      class="input-field @error('password') border-red-400 @enderror"
      placeholder="••••••••">
    @error('password')
    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
  </div>

  <div class="flex items-center">
    <input id="remember" name="remember" type="checkbox"
      class="h-4 w-4 text-brand-600 border-slate-300 rounded focus:ring-brand-500">
    <label for="remember" class="ml-2 text-sm text-slate-600">Remember me</label>
  </div>

  <button type="submit" class="btn-primary w-full mt-2">
    Sign in to dashboard
  </button>
</form>

<p class="mt-5 text-center text-sm text-slate-500">
  Don't have an account?
  <a href="{{ route('register') }}" class="font-medium text-brand-600 hover:text-brand-500">Create one free</a>
</p>
@endsection
