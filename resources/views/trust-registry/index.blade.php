@extends('layouts.app')
@section('title', 'Trust Registry')
@section('page-title', 'Merchant Trust Registry')

@section('content')
<div class="max-w-5xl mx-auto space-y-5">

  {{-- Trust score header --}}
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- Trust score card --}}
    <div class="card p-6 text-center">
      @php
      $scoreColor = $trustScore >= 0.8
      ? '#059669'
      : ($trustScore >= 0.5 ? '#d97706' : '#dc2626');
      $scoreBg = $trustScore >= 0.8
      ? '#ecfdf5'
      : ($trustScore >= 0.5 ? '#fffbeb' : '#fef2f2');
      $scoreLabel = $trustScore >= 0.8
      ? 'Good Standing'
      : ($trustScore >= 0.5 ? 'Moderate Risk' : 'High Risk');
      @endphp
      <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-3">
        Trust Score
      </p>
      <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-3"
        style="background:{{ $scoreBg }}; border: 3px solid {{ $scoreColor }};">
        <span class="text-2xl font-bold" style="color:{{ $scoreColor }};">
          {{ number_format($trustScore * 100) }}
        </span>
      </div>
      <span class="text-xs font-semibold px-3 py-1 rounded-full"
        style="background:{{ $scoreBg }};color:{{ $scoreColor }};">
        {{ $scoreLabel }}
      </span>
      <p class="text-xs text-slate-400 mt-3">
        Based on {{ $entries->total() }} registry events
      </p>
    </div>

    {{-- Penalty summary --}}
    <div class="card p-5">
      <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">
        Penalty Summary
      </h3>
      <div class="space-y-3">
        <div class="flex items-center justify-between">
          <span class="text-sm text-slate-600">Total Penalty Points</span>
          <span class="text-lg font-bold text-red-600">{{ $totalPenalty }}</span>
        </div>
        <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
          <div class="h-full rounded-full"
            style="width:{{ min(100, $totalPenalty / 2) }}%;background:#dc2626;">
          </div>
        </div>
        <p class="text-xs text-slate-400">
          Max threshold: 200 points before trust score reaches 0
        </p>
      </div>
    </div>

    {{-- Event breakdown --}}
    <div class="card p-5">
      <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">
        Event Breakdown
      </h3>
      <div class="space-y-2">
        @php
        $eventLabels = [
        'chargeback_filed' => ['label' => 'Chargebacks Filed', 'color' => '#dc2626', 'bg' => '#fef2f2'],
        'fraud_confirmed' => ['label' => 'Fraud Confirmed', 'color' => '#dc2626', 'bg' => '#fef2f2'],
        'dispute_lost' => ['label' => 'Disputes Lost', 'color' => '#d97706', 'bg' => '#fffbeb'],
        'suspicious_pattern' => ['label' => 'Suspicious Patterns', 'color' => '#d97706', 'bg' => '#fffbeb'],
        'dispute_won' => ['label' => 'Disputes Won', 'color' => '#059669', 'bg' => '#ecfdf5'],
        ];
        @endphp
        @foreach($eventLabels as $type => $config)
        @if(isset($eventCounts[$type]) && $eventCounts[$type] > 0)
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2">
            <div class="w-2 h-2 rounded-full"
              style="background:{{ $config['color'] }};"></div>
            <span class="text-xs text-slate-600">{{ $config['label'] }}</span>
          </div>
          <span class="text-xs font-semibold px-2 py-0.5 rounded-full"
            style="background:{{ $config['bg'] }};color:{{ $config['color'] }};">
            {{ $eventCounts[$type] }}
          </span>
        </div>
        @endif
        @endforeach
        @if(empty(array_filter($eventCounts)))
        <p class="text-xs text-slate-400">No events recorded yet</p>
        @endif
      </div>
    </div>
  </div>

  {{-- Registry table --}}
  <div class="card overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
      <h3 class="text-sm font-semibold text-slate-800">Registry Entries</h3>
      <div class="flex items-center gap-2">
        <span class="badge badge-slate">Append-only</span>
        <span class="text-xs text-slate-400">{{ $entries->total() }} entries</span>
      </div>
    </div>

    @if($entries->isEmpty())
    <div class="px-6 py-16 text-center">
      <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center mx-auto mb-3">
        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </div>
      <p class="text-sm font-medium text-slate-600">Clean record</p>
      <p class="text-xs text-slate-400 mt-1">
        No trust registry events have been recorded yet.
      </p>
    </div>
    @else
    <div class="divide-y divide-slate-50">
      @foreach($entries as $entry)
      @php
      $eventConfig = [
      'chargeback_filed' => ['bg' => '#fef2f2', 'color' => '#dc2626', 'border' => '#fca5a5'],
      'fraud_confirmed' => ['bg' => '#fef2f2', 'color' => '#dc2626', 'border' => '#fca5a5'],
      'dispute_lost' => ['bg' => '#fffbeb', 'color' => '#d97706', 'border' => '#fcd34d'],
      'suspicious_pattern' => ['bg' => '#fffbeb', 'color' => '#d97706', 'border' => '#fcd34d'],
      'dispute_won' => ['bg' => '#ecfdf5', 'color' => '#059669', 'border' => '#6ee7b7'],
      ];
      $ec = $eventConfig[$entry->event_type->value] ?? ['bg' => '#f1f5f9', 'color' => '#64748b', 'border' => '#cbd5e1'];
      @endphp
      <div class="px-5 py-3.5 hover:bg-slate-50 transition-colors">
        <div class="flex items-start gap-4">

          {{-- Event icon --}}
          <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 mt-0.5"
            style="background:{{ $ec['bg'] }};border:1px solid {{ $ec['border'] }};">
            <div class="w-2 h-2 rounded-full" style="background:{{ $ec['color'] }};"></div>
          </div>

          {{-- Content --}}
          <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between gap-3">
              <div>
                <div class="flex items-center gap-2 flex-wrap">
                  <span class="text-xs font-semibold px-2 py-0.5 rounded-md"
                    style="background:{{ $ec['bg'] }};color:{{ $ec['color'] }};">
                    {{ $entry->event_type->label() }}
                  </span>
                  @if($entry->penalty_points > 0)
                  <span class="text-xs font-medium text-red-600">
                    +{{ $entry->penalty_points }} penalty points
                  </span>
                  @else
                  <span class="text-xs font-medium text-emerald-600">
                    No penalty
                  </span>
                  @endif
                </div>

                @if($entry->notes)
                <p class="text-xs text-slate-500 mt-1">{{ $entry->notes }}</p>
                @endif

                @if($entry->transaction)
                <p class="text-xs font-mono text-slate-400 mt-1">
                  TX: {{ substr($entry->transaction->ulid, 0, 20) }}...
                </p>
                @endif
              </div>

              <div class="text-right shrink-0">
                <p class="text-xs text-slate-500">
                  {{ $entry->created_at->format('M d, Y') }}
                </p>
                <p class="text-xs font-mono text-slate-400">
                  {{ $entry->created_at->format('H:i:s') }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
      @endforeach
    </div>

    @if($entries->hasPages())
    <div class="px-6 py-4 border-t border-slate-100">
      {{ $entries->links() }}
    </div>
    @endif
    @endif
  </div>

  {{-- Append-only notice --}}
  <div class="flex items-start gap-3 px-4 py-3 rounded-xl"
    style="background:#f8fafc;border:1px solid #e2e8f0;">
    <svg class="w-4 h-4 text-slate-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
    <p class="text-xs text-slate-500">
      The trust registry is <strong class="text-slate-700">append-only</strong>.
      No entries can be modified or deleted after creation.
      This ensures the integrity of the merchant reputation record
      and provides a tamper-proof audit trail for card network compliance.
    </p>
  </div>

</div>
@endsection
