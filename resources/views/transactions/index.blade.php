@extends('layouts.app')
@section('title', 'Transactions')
@section('page-title', 'Transactions')

@section('content')
<div class="max-w-7xl mx-auto space-y-5">

  {{-- Stats row --}}
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    @php
    $tStats = [
    ['label' => 'Total', 'value' => $stats['total'], 'color' => '#4f46e5', 'bg' => '#eef2ff'],
    ['label' => 'Approved', 'value' => $stats['approved'], 'color' => '#059669', 'bg' => '#ecfdf5'],
    ['label' => 'Step-Up', 'value' => $stats['step_up'], 'color' => '#d97706', 'bg' => '#fffbeb'],
    ['label' => 'Declined', 'value' => $stats['declined'], 'color' => '#dc2626', 'bg' => '#fef2f2'],
    ];
    @endphp
    @foreach($tStats as $s)
    <div class="card p-4 flex items-center gap-4">
      <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
        style="background:{{ $s['bg'] }};">
        <span class="text-sm font-bold" style="color:{{ $s['color'] }};">
          {{ number_format($s['value']) }}
        </span>
      </div>
      <p class="text-sm font-medium text-slate-600">{{ $s['label'] }}</p>
    </div>
    @endforeach
  </div>

  {{-- Filters --}}
  <form method="GET" action="{{ route('transactions') }}" class="card p-4">
    <div class="flex items-center gap-3 flex-wrap">
      <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
      </svg>

      {{-- Search --}}
      <input type="text" name="search"
        value="{{ request('search') }}"
        placeholder="Search BIN or last4..."
        class="input-field py-1.5 text-xs w-40">

      {{-- Decision filter --}}
      <select name="decision" class="input-field py-1.5 text-xs w-auto"
        onchange="this.form.submit()">
        <option value="">All decisions</option>
        <option value="allow" {{ request('decision') === 'allow'   ? 'selected' : '' }}>Approved</option>
        <option value="step_up" {{ request('decision') === 'step_up' ? 'selected' : '' }}>Step-Up</option>
        <option value="decline" {{ request('decision') === 'decline' ? 'selected' : '' }}>Declined</option>
      </select>

      {{-- Risk level filter --}}
      <select name="risk_level" class="input-field py-1.5 text-xs w-auto"
        onchange="this.form.submit()">
        <option value="">All risk levels</option>
        <option value="low" {{ request('risk_level') === 'low'    ? 'selected' : '' }}>Low Risk</option>
        <option value="medium" {{ request('risk_level') === 'medium' ? 'selected' : '' }}>Medium Risk</option>
        <option value="high" {{ request('risk_level') === 'high'   ? 'selected' : '' }}>High Risk</option>
      </select>

      {{-- Date range --}}
      <input type="date" name="from"
        value="{{ request('from') }}"
        class="input-field py-1.5 text-xs w-auto">
      <span class="text-xs text-slate-400">to</span>
      <input type="date" name="to"
        value="{{ request('to') }}"
        class="input-field py-1.5 text-xs w-auto">

      <button type="submit" class="btn-primary py-1.5 text-xs">Search</button>

      @if(request()->hasAny(['search', 'decision', 'risk_level', 'from', 'to']))
      <a href="{{ route('transactions') }}"
        class="text-xs text-slate-500 hover:text-slate-700 underline">
        Clear
      </a>
      @endif

      <span class="text-xs text-slate-400 ml-auto">
        {{ $transactions->total() }} transactions
      </span>
    </div>
  </form>
  {{-- Export button --}}
  <div class="flex justify-end">
    <a href="{{ route('transactions.export', request()->query()) }}"
      class="btn-secondary text-xs flex items-center gap-2">
      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
      </svg>
      Export CSV
    </a>
  </div>

  {{-- Table --}}
  <div class="card overflow-hidden">
    @if($transactions->isEmpty())
    <div class="px-6 py-16 text-center">
      <div class="w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center mx-auto mb-3">
        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13 10V3L4 14h7v7l9-11h-7z" />
        </svg>
      </div>
      <p class="text-sm font-medium text-slate-600">No transactions found</p>
      <p class="text-xs text-slate-400 mt-1">Try adjusting your filters</p>
    </div>
    @else
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead>
          <tr class="border-b border-slate-100">
            <th class="text-left text-xs font-medium text-slate-400 uppercase tracking-wider px-5 py-3">Card</th>
            <th class="text-left text-xs font-medium text-slate-400 uppercase tracking-wider px-4 py-3">Amount</th>
            <th class="text-left text-xs font-medium text-slate-400 uppercase tracking-wider px-4 py-3">Risk Score</th>
            <th class="text-left text-xs font-medium text-slate-400 uppercase tracking-wider px-4 py-3">Decision</th>
            <th class="text-left text-xs font-medium text-slate-400 uppercase tracking-wider px-4 py-3">Geo</th>
            <th class="text-left text-xs font-medium text-slate-400 uppercase tracking-wider px-4 py-3">Evidence</th>
            <th class="text-left text-xs font-medium text-slate-400 uppercase tracking-wider px-4 py-3">Time</th>
            <th class="px-4 py-3"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
          @foreach($transactions as $tx)
          @php
          $score = $tx->risk_score;
          $scoreColor = $score < 0.4 ? '#059669' : ($score < 0.7 ? '#d97706' : '#dc2626' );
            $dotColors=[ 'allow'=> ['bg' => '#ecfdf5', 'dot' => '#059669'],
            'step_up' => ['bg' => '#fffbeb', 'dot' => '#d97706'],
            'decline' => ['bg' => '#fef2f2', 'dot' => '#dc2626'],
            ];
            $dc = $dotColors[$tx->decision->value] ?? ['bg' => '#f1f5f9', 'dot' => '#94a3b8'];
            @endphp
            <tr class="hover:bg-slate-50 transition-colors">
              <td class="px-5 py-3">
                <div class="flex items-center gap-2.5">
                  <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                    style="background:{{ $dc['bg'] }};">
                    <div class="w-2 h-2 rounded-full"
                      style="background:{{ $dc['dot'] }};"></div>
                  </div>
                  <div>
                    <p class="text-xs font-mono font-medium text-slate-700">
                      ****{{ $tx->card_last4 }}
                    </p>
                    <p class="text-xs text-slate-400">BIN {{ $tx->card_bin }}</p>
                  </div>
                </div>
              </td>
              <td class="px-4 py-3">
                <p class="text-sm font-semibold text-slate-800">
                  {{ $tx->currency }} {{ number_format($tx->amount / 100, 2) }}
                </p>
              </td>
              <td class="px-4 py-3">
                <div class="flex items-center gap-2">
                  <div class="w-16 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full rounded-full"
                      style="width:{{ round($score * 100) }}%;background:{{ $scoreColor }};">
                    </div>
                  </div>
                  <span class="text-xs font-mono font-semibold"
                    style="color:{{ $scoreColor }};">
                    {{ number_format($score, 3) }}
                  </span>
                </div>
              </td>
              <td class="px-4 py-3">
                <span class="{{ $tx->decision->badgeClass() }}">
                  {{ $tx->decision->label() }}
                </span>
              </td>
              <td class="px-4 py-3">
                <div class="text-xs text-slate-500">
                  <span class="font-medium">{{ $tx->ip_country ?? '?' }}</span>
                  <span class="text-slate-300 mx-1">→</span>
                  <span class="font-medium">{{ $tx->card_country ?? '?' }}</span>
                </div>
              </td>
              <td class="px-4 py-3">
                @if($tx->evidenceBundle)
                <div class="flex items-center gap-1" style="color:#059669;">
                  <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                  </svg>
                  <span class="text-xs font-medium">Locked</span>
                </div>
                @else
                <span class="text-xs text-slate-300">—</span>
                @endif
              </td>
              <td class="px-4 py-3">
                <p class="text-xs text-slate-500">
                  {{ $tx->created_at->format('M d, H:i') }}
                </p>
                <p class="text-xs text-slate-400">
                  {{ $tx->created_at->diffForHumans() }}
                </p>
              </td>
              <td class="px-4 py-3">
                <a href="{{ route('transactions.show', $tx->ulid) }}"
                  class="btn-secondary text-xs py-1 px-3">
                  View
                </a>
              </td>
            </tr>
            @endforeach
        </tbody>
      </table>
    </div>

    @if($transactions->hasPages())
    <div class="px-6 py-4 border-t border-slate-100">
      {{ $transactions->links() }}
    </div>
    @endif
    @endif
  </div>
</div>
@endsection
