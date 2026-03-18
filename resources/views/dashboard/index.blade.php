@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto">

  {{-- Welcome banner --}}
  <div class="card p-6 mb-6 bg-gradient-to-r from-brand-600 to-brand-700 border-0 text-white">
    <div class="flex items-start justify-between">
      <div>
        <h2 class="text-lg font-bold">Welcome to Chargeback Shield</h2>
        <p class="text-brand-100 text-sm mt-1">
          Your real-time chargeback protection layer is active. Start by integrating the API or running a simulation.
        </p>
      </div>
      <div class="flex gap-2 shrink-0">
        <a href="#" class="px-4 py-2 bg-white text-brand-700 text-sm font-medium rounded-lg hover:bg-brand-50 transition-colors">
          View API docs
        </a>
        <a href="{{ route('simulate') }}" class="px-4 py-2 bg-brand-500 text-white text-sm font-medium rounded-lg hover:bg-brand-400 border border-brand-400 transition-colors">
          Run simulation
        </a>
      </div>
    </div>
  </div>

  {{-- Stat cards --}}
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach([
    ['label' => 'Transactions', 'value' => '0', 'delta' => 'No data yet', 'color' => 'blue', 'icon' => 'tx'],
    ['label' => 'Flagged', 'value' => '0', 'delta' => 'No data yet', 'color' => 'amber', 'icon' => 'flag'],
    ['label' => 'Chargebacks', 'value' => '0', 'delta' => 'No data yet', 'color' => 'red', 'icon' => 'dispute'],
    ['label' => 'Disputes Won', 'value' => '0', 'delta' => 'No data yet', 'color' => 'green', 'icon' => 'win'],
    ] as $stat)
    <div class="card p-5">
      <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">{{ $stat['label'] }}</p>
      <p class="text-3xl font-bold text-slate-800 mt-1">{{ $stat['value'] }}</p>
      <p class="text-xs text-slate-400 mt-1">{{ $stat['delta'] }}</p>
    </div>
    @endforeach
  </div>

  {{-- Empty state for transaction feed --}}
  <div class="card p-12 text-center">
    <div class="w-14 h-14 bg-brand-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
      <svg class="w-7 h-7 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
          d="M13 10V3L4 14h7v7l9-11h-7z" />
      </svg>
    </div>
    <h3 class="text-base font-semibold text-slate-800 mb-2">No transactions yet</h3>
    <p class="text-sm text-slate-500 max-w-sm mx-auto">
      Integrate the Chargeback Shield API or run a simulation to start seeing live transaction data here.
    </p>
    <div class="flex gap-3 justify-center mt-5">
      <a href="{{ route('simulate') }}" class="btn-primary">Run a simulation</a>
      <a href="#" class="btn-secondary">View API docs</a>
    </div>
  </div>

</div>
@endsection
