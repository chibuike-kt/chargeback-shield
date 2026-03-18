@extends('layouts.app')
@section('title', 'Transaction ' . substr($transaction->ulid, 0, 12))
@section('page-title', 'Transaction Detail')

@section('content')
<div class="max-w-5xl mx-auto space-y-5">

  {{-- Back --}}
  <a href="{{ route('transactions') }}"
    class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-700">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
    </svg>
    Back to transactions
  </a>

  {{-- Header card --}}
  <div class="card p-6">
    <div class="flex items-start justify-between">
      <div>
        <div class="flex items-center gap-3 mb-2">
          <span class="{{ $transaction->decision->badgeClass() }} text-sm px-3 py-1">
            {{ $transaction->decision->label() }}
          </span>
          <span class="{{ $transaction->risk_level->badgeClass() }}">
            {{ $transaction->risk_level->label() }}
          </span>
          @if($transaction->evidenceBundle)
          <span class="badge badge-green">Evidence Locked</span>
          @endif
        </div>
        <h2 class="text-base font-bold text-slate-800 font-mono">
          {{ $transaction->ulid }}
        </h2>
        <p class="text-sm text-slate-500 mt-1">
          {{ $transaction->currency }}
          {{ number_format($transaction->amount / 100, 2) }}
          &nbsp;·&nbsp;
          {{ $transaction->created_at->format('M d, Y H:i:s') }} UTC
        </p>
      </div>
      @php
      $score = $transaction->risk_score;
      $scoreColor = $score < 0.4 ? '#059669' : ($score < 0.7 ? '#d97706' : '#dc2626' );
        $scoreBg=$score < 0.4 ? '#ecfdf5' : ($score < 0.7 ? '#fffbeb' : '#fef2f2' );
        @endphp
        <div class="text-center px-5 py-3 rounded-xl"
        style="background:{{ $scoreBg }};">
        <p class="text-xs text-slate-500 mb-1">Risk Score</p>
        <p class="text-3xl font-bold font-mono"
          style="color:{{ $scoreColor }};">
          {{ number_format($score, 3) }}
        </p>
    </div>
  </div>
</div>

{{-- Detail grid --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

  {{-- Card + Transaction --}}
  <div class="card p-5">
    <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">
      Card & Transaction
    </h3>
    <div class="space-y-3">
      <div>
        <p class="text-xs text-slate-400">Card Number</p>
        <p class="text-sm font-mono font-medium text-slate-700 mt-0.5">
          {{ $transaction->card_bin }} •••• •••• {{ $transaction->card_last4 }}
        </p>
      </div>
      <div>
        <p class="text-xs text-slate-400">Card Country</p>
        <p class="text-sm font-medium text-slate-700 mt-0.5">
          {{ $transaction->card_country ?? '—' }}
        </p>
      </div>
      <div>
        <p class="text-xs text-slate-400">Amount</p>
        <p class="text-sm font-bold text-slate-800 mt-0.5">
          {{ $transaction->currency }}
          {{ number_format($transaction->amount / 100, 2) }}
        </p>
      </div>
      <div>
        <p class="text-xs text-slate-400">Merchant Category</p>
        <p class="text-sm font-mono text-slate-700 mt-0.5">
          {{ $transaction->merchant_category ?? '—' }}
        </p>
      </div>
    </div>
  </div>

  {{-- Network --}}
  <div class="card p-5">
    <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">
      Network & Device
    </h3>
    <div class="space-y-3">
      <div>
        <p class="text-xs text-slate-400">IP Address</p>
        <p class="text-sm font-mono text-slate-700 mt-0.5">
          {{ $transaction->ip_address ?? '—' }}
        </p>
      </div>
      <div>
        <p class="text-xs text-slate-400">IP Country / City</p>
        <p class="text-sm font-medium text-slate-700 mt-0.5">
          {{ $transaction->ip_country ?? '—' }}
          {{ $transaction->ip_city ? '· ' . $transaction->ip_city : '' }}
        </p>
      </div>
      <div>
        <p class="text-xs text-slate-400">Device Fingerprint</p>
        <p class="text-xs font-mono text-slate-600 mt-0.5 truncate">
          {{ $transaction->device_fingerprint ?? 'None' }}
        </p>
      </div>
      <div>
        <p class="text-xs text-slate-400">Session Age</p>
        <p class="text-sm font-medium text-slate-700 mt-0.5">
          {{ $transaction->session_age_seconds }}s
          @if($transaction->session_age_seconds < 60)
            <span class="text-xs text-red-500">(very new)</span>
            @elseif($transaction->session_age_seconds < 300)
              <span class="text-xs text-amber-500">(new)</span>
              @else
              <span class="text-xs text-emerald-500">(established)</span>
              @endif
        </p>
      </div>
    </div>
  </div>

  {{-- Evidence --}}
  <div class="card p-5">
    <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">
      Evidence Bundle
    </h3>
    @if($transaction->evidenceBundle)
    <div class="space-y-3">
      <div>
        <p class="text-xs text-slate-400">Bundle ID</p>
        <p class="text-xs font-mono text-slate-600 mt-0.5 break-all">
          {{ $transaction->evidenceBundle->ulid }}
        </p>
      </div>
      <div>
        <p class="text-xs text-slate-400">Signature</p>
        <div class="flex items-center gap-1.5 mt-0.5">
          @if($transaction->evidenceBundle->is_verified)
          <svg class="w-4 h-4" style="color:#059669;" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
          </svg>
          <span class="text-xs font-medium" style="color:#059669;">HMAC-SHA256 Verified</span>
          @else
          <span class="text-xs font-medium text-red-600">Not verified</span>
          @endif
        </div>
      </div>
      <div>
        <p class="text-xs text-slate-400">Locked At</p>
        <p class="text-xs text-slate-600 mt-0.5">
          {{ $transaction->evidenceBundle->created_at->format('M d, Y H:i:s') }} UTC
        </p>
      </div>
      <div>
        <p class="text-xs text-slate-400">HMAC Signature</p>
        <p class="text-xs font-mono text-slate-400 mt-0.5 truncate">
          {{ substr($transaction->evidenceBundle->hmac_signature, 0, 32) }}...
        </p>
      </div>
    </div>
    @else
    <div class="flex flex-col items-center justify-center h-32 text-center">
      <p class="text-xs text-slate-400">No evidence bundle</p>
      <p class="text-xs text-slate-300 mt-1">Declined transactions do not generate evidence</p>
    </div>
    @endif
  </div>
</div>

{{-- Risk signal breakdown --}}
@if($transaction->riskSignalLogs->isNotEmpty())
<div class="card overflow-hidden">
  <div class="px-5 py-4 border-b border-slate-100">
    <h3 class="text-sm font-semibold text-slate-800">Risk Signal Breakdown</h3>
    <p class="text-xs text-slate-400 mt-0.5">
      Composite score: {{ number_format($transaction->risk_score, 4) }}
      — each signal's contribution to the final decision
    </p>
  </div>
  <div class="p-5 space-y-3">
    @foreach($transaction->riskSignalLogs as $signal)
    @php
    $sc = $signal->normalized_score;
    $sColor = $sc < 0.4 ? '#059669' : ($sc < 0.7 ? '#d97706' : '#dc2626' );
      @endphp
      <div class="flex items-center gap-4">
      <div class="w-36 shrink-0">
        <p class="text-xs font-medium text-slate-700">
          {{ str_replace('_', ' ', ucfirst($signal->signal_name)) }}
        </p>
        <p class="text-xs text-slate-400 mt-0.5">weight: {{ $signal->weight * 100 }}%</p>
      </div>
      <div class="flex-1">
        <div class="flex items-center gap-2 mb-1">
          <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
            <div class="h-full rounded-full transition-all"
              style="width:{{ round($sc * 100) }}%;background:{{ $sColor }};"></div>
          </div>
          <span class="text-xs font-mono font-semibold w-10 text-right"
            style="color:{{ $sColor }};">
            {{ number_format($sc, 3) }}
          </span>
        </div>
        <p class="text-xs text-slate-400">{{ $signal->raw_value }}</p>
      </div>
      <div class="w-20 text-right shrink-0">
        <p class="text-xs text-slate-500">contribution</p>
        <p class="text-xs font-mono font-semibold text-slate-700">
          {{ number_format($signal->weighted_contribution, 4) }}
        </p>
      </div>
  </div>
  @endforeach

  {{-- Total --}}
  <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
    <p class="text-sm font-semibold text-slate-700">Composite Risk Score</p>
    <p class="text-lg font-bold font-mono"
      style="color:{{ $transaction->risk_score < 0.4 ? '#059669' : ($transaction->risk_score < 0.7 ? '#d97706' : '#dc2626') }};">
      {{ number_format($transaction->risk_score, 4) }}
    </p>
  </div>
</div>
</div>
@endif

{{-- Linked dispute --}}
@if($transaction->dispute)
<div class="card p-5">
  <div class="flex items-center justify-between">
    <div>
      <h3 class="text-sm font-semibold text-slate-800">Linked Dispute</h3>
      <p class="text-xs text-slate-400 mt-0.5">
        A chargeback was filed against this transaction
      </p>
    </div>
    <a href="{{ route('disputes.show', $transaction->dispute->ulid) }}"
      class="btn-primary text-xs">
      View Dispute →
    </a>
  </div>
  <div class="mt-4 flex items-center gap-4">
    <span class="{{ $transaction->dispute->status->badgeClass() }}">
      {{ $transaction->dispute->status->label() }}
    </span>
    <span class="text-xs text-slate-500">
      {{ $transaction->dispute->reason_code }}
      — {{ $transaction->dispute->reason_description }}
    </span>
    <span class="text-xs text-slate-400">
      Filed {{ $transaction->dispute->filed_at?->diffForHumans() }}
    </span>
  </div>
</div>
@endif

</div>
@endsection
