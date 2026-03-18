@extends('layouts.app')
@section('title', $title)
@section('page-title', $title)

@section('content')
<div class="max-w-7xl mx-auto">
  <div class="card p-16 text-center">
    <div class="w-14 h-14 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
      <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
    </div>
    <h3 class="text-base font-semibold text-slate-700">{{ $title }} — coming in the next phase</h3>
    <p class="text-sm text-slate-400 mt-2">This module is being built incrementally.</p>
  </div>
</div>
