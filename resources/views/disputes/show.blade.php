@extends('layouts.app')
@section('title', 'Dispute ' . substr($dispute->ulid, 0, 12))
@section('page-title', 'Dispute Detail')

@section('content')
<div class="max-w-5xl mx-auto space-y-5">

  {{-- Back + actions --}}
  <div class="flex items-center justify-between">
    <a href="{{ route('disputes') }}" class="flex items-center gap-2 text-sm text-slate-500 hover:text-slate-700">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
      </svg>
      Back to disputes
    </a>
    @if($dispute->response_document)
    <a href="{{ route('disputes.pdf', $dispute->ulid) }}" class="btn-primary">
      Download PDF Response
    </a>
    @endif
  </div>

  {{-- Dispute header card --}}
  <div class="card p-6">
    <div class="flex items-start justify-between">
      <div>
        <div class="flex items-center gap-3 mb-2">
          <span class="{{ $dispute->status->badgeClass() }} text-sm px-3 py-1">
            {{ $dispute->status->label() }}
          </span>
          <span class="badge {{ $dispute->network->value === 'visa' ? 'badge-blue' : 'badge-yellow' }}">
            {{ $dispute->network->label() }}
          </span>
          @if($dispute->response_document)
          <span class="badge badge-green">Response Ready</span>
          @endif
        </div>
        <h2 class="text-lg font-bold text-slate-800">
          {{ $dispute->reason_code }} — {{ $dispute->reason_description }}
        </h2>
        <p class="text-sm text-slate-500 mt-1 font-mono">
          {{ $dispute->ulid }}
        </p>
      </div>
      <div class="text-right text-sm text-slate-500">
        <p>Filed: <strong class="text-slate-700">{{ $dispute->filed_at?->format('M d, Y H:i') ?? '—' }}</strong></p>
        @if($dispute->responded_at)
        <p class="mt-1">Responded: <strong class="text-slate-700">{{ $dispute->responded_at->format('M d, Y H:i') }}</strong></p>
        @endif
        @if($dispute->resolved_at)
        <p class="mt-1">Resolved: <strong class="text-slate-700">{{ $dispute->resolved_at->format('M d, Y H:i') }}</strong></p>
        @endif
      </div>
    </div>
  </div>

  {{-- Transaction + Evidence --}}
  <div class="grid grid-cols-2 gap-4">
    <div class="card p-5">
      <h3 class="text-sm font-semibold text-slate-700 mb-4">Transaction</h3>
      <div class="space-y-3">
        <div>
          <p class="text-xs text-slate-400">Transaction ID</p>
          <p class="text-xs font-mono text-slate-700 mt-0.5">{{ $dispute->transaction->ulid }}</p>
        </div>
        <div>
          <p class="text-xs text-slate-400">Amount</p>
          <p class="text-base font-bold text-slate-800 mt-0.5">
            {{ $dispute->transaction->currency }}
            {{ number_format($dispute->transaction->amount / 100, 2) }}
          </p>
        </div>
        <div>
          <p class="text-xs text-slate-400">Risk Score / Decision</p>
          <div class="flex items-center gap-2 mt-0.5">
            <span class="text-sm font-semibold text-slate-700">
              {{ number_format($dispute->transaction->risk_score, 3) }}
            </span>
            <span class="{{ $dispute->transaction->decision->badgeClass() }}">
              {{ $dispute->transaction->decision->label() }}
            </span>
          </div>
        </div>
        <div>
          <p class="text-xs text-slate-400">Processed At</p>
          <p class="text-xs text-slate-700 mt-0.5">
            {{ $dispute->transaction->created_at->format('M d, Y H:i:s') }} UTC
          </p>
        </div>
      </div>
    </div>

    <div class="card p-5">
      <h3 class="text-sm font-semibold text-slate-700 mb-4">Evidence Bundle</h3>
      @if($dispute->transaction->evidenceBundle)
      <div class="space-y-3">
        <div>
          <p class="text-xs text-slate-400">Bundle ID</p>
          <p class="text-xs font-mono text-slate-700 mt-0.5">
            {{ $dispute->transaction->evidenceBundle->ulid }}
          </p>
        </div>
        <div>
          <p class="text-xs text-slate-400">Signature Status</p>
          <div class="flex items-center gap-1.5 mt-0.5">
            @if($dispute->transaction->evidenceBundle->is_verified)
            <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span class="text-xs font-medium text-emerald-600">HMAC-SHA256 Verified</span>
            @else
            <span class="text-xs font-medium text-red-600">Signature Invalid</span>
            @endif
          </div>
        </div>
        <div>
          <p class="text-xs text-slate-400">Locked At</p>
          <p class="text-xs text-slate-700 mt-0.5">
            {{ $dispute->transaction->evidenceBundle->created_at->format('M d, Y H:i:s') }} UTC
          </p>
        </div>
        <div>
          <p class="text-xs text-slate-400">HMAC Signature</p>
          <p class="text-xs font-mono text-slate-500 mt-0.5 truncate">
            {{ substr($dispute->transaction->evidenceBundle->hmac_signature, 0, 32) }}...
          </p>
        </div>
      </div>
      @else
      <p class="text-sm text-slate-400">No evidence bundle available.</p>
      @endif
    </div>
  </div>

  {{-- Response document --}}
  @if($dispute->response_document)
  <div class="card overflow-hidden">
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
      <h3 class="text-sm font-semibold text-slate-800">Generated Response Document</h3>
      <a href="{{ route('disputes.pdf', $dispute->ulid) }}" class="btn-primary text-xs">
        Download PDF
      </a>
    </div>

    <div class="p-6 space-y-5">
      {{-- Strategy --}}
      <div class="bg-indigo-50 border border-indigo-200 border-l-4 border-l-indigo-500 rounded-lg p-4">
        <h4 class="text-xs font-bold text-indigo-700 uppercase tracking-wider mb-2">Response Strategy</h4>
        <p class="text-sm text-indigo-900">{{ $dispute->response_document['response_strategy'] }}</p>
      </div>

      <div class="bg-emerald-50 border border-emerald-200 border-l-4 border-l-emerald-500 rounded-lg p-4">
        <h4 class="text-xs font-bold text-emerald-700 uppercase tracking-wider mb-2">Winning Argument</h4>
        <p class="text-sm text-emerald-900">{{ $dispute->response_document['winning_argument'] }}</p>
      </div>

      {{-- Risk signals table --}}
      <div>
        <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">
          Risk Signal Breakdown at Authorization
        </h4>
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="border-b border-slate-100">
                <th class="text-left text-xs font-medium text-slate-400 uppercase tracking-wider py-2 pr-4">Signal</th>
                <th class="text-left text-xs font-medium text-slate-400 uppercase tracking-wider py-2 pr-4">Value</th>
                <th class="text-left text-xs font-medium text-slate-400 uppercase tracking-wider py-2 pr-4">Score</th>
                <th class="text-left text-xs font-medium text-slate-400 uppercase tracking-wider py-2">Contribution</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
              @foreach($dispute->response_document['risk_signals'] as $signal)
              <tr>
                <td class="py-2.5 pr-4 font-medium text-slate-700">
                  {{ str_replace('_', ' ', ucfirst($signal['signal'])) }}
                </td>
                <td class="py-2.5 pr-4 text-slate-500 text-xs">{{ $signal['value'] }}</td>
                <td class="py-2.5 pr-4">
                  @php
                  $s = $signal['score'];
                  $c = $s < 0.4 ? 'text-emerald-600' : ($s < 0.7 ? 'text-amber-600' : 'text-red-600' );
                    @endphp
                    <span class="font-mono text-xs font-semibold {{ $c }}">
                    {{ number_format($s, 3) }}
                    </span>
                </td>
                <td class="py-2.5 text-xs text-slate-500">
                  {{ number_format($signal['contribution'], 4) }}
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

      {{-- Summary --}}
      <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Document Summary</h4>
        <p class="text-xs text-slate-600 leading-relaxed">
          {{ $dispute->response_document['summary'] }}
        </p>
      </div>
    </div>
  </div>
  @endif

</div>
@endsection
