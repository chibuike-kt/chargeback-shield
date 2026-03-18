@extends('layouts.app')
@section('title', 'Disputes')
@section('page-title', 'Dispute Management')

@section('content')
<div class="max-w-7xl mx-auto space-y-5">

  {{-- Header --}}
  <div class="flex items-center justify-between">
    <div>
      <p class="text-sm text-slate-500 mt-0.5">
        All chargebacks filed against your transactions
      </p>
    </div>
    <div class="flex items-center gap-2">
      <span class="badge badge-blue">{{ $disputes->total() }} total</span>
    </div>
  </div>

  {{-- Table --}}
  <div class="card overflow-hidden">
    @if($disputes->isEmpty())
    <div class="px-6 py-16 text-center">
      <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
        <svg class="w-7 h-7 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
        </svg>
      </div>
      <h3 class="text-base font-semibold text-slate-700">No disputes filed</h3>
      <p class="text-sm text-slate-400 mt-1 max-w-sm mx-auto">
        When a chargeback is filed against one of your transactions,
        Chargeback Shield will auto-generate the response document here.
      </p>
    </div>
    @else
    <table class="w-full">
      <thead>
        <tr class="border-b border-slate-100">
          <th class="text-left text-xs font-medium text-slate-400 uppercase tracking-wider px-6 py-3">Dispute</th>
          <th class="text-left text-xs font-medium text-slate-400 uppercase tracking-wider px-4 py-3">Transaction</th>
          <th class="text-left text-xs font-medium text-slate-400 uppercase tracking-wider px-4 py-3">Network</th>
          <th class="text-left text-xs font-medium text-slate-400 uppercase tracking-wider px-4 py-3">Amount</th>
          <th class="text-left text-xs font-medium text-slate-400 uppercase tracking-wider px-4 py-3">Status</th>
          <th class="text-left text-xs font-medium text-slate-400 uppercase tracking-wider px-4 py-3">Response</th>
          <th class="text-left text-xs font-medium text-slate-400 uppercase tracking-wider px-4 py-3">Filed</th>
          <th class="px-4 py-3"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-50">
        @foreach($disputes as $dispute)
        <tr class="hover:bg-slate-50 transition-colors">
          <td class="px-6 py-3.5">
            <p class="text-xs font-mono font-medium text-slate-700">
              {{ substr($dispute->ulid, 0, 16) }}...
            </p>
            <p class="text-xs text-slate-400 mt-0.5">
              {{ $dispute->reason_code }} — {{ Str::limit($dispute->reason_description, 30) }}
            </p>
          </td>
          <td class="px-4 py-3.5">
            <p class="text-xs font-mono text-slate-600">
              {{ substr($dispute->transaction->ulid, 0, 14) }}...
            </p>
          </td>
          <td class="px-4 py-3.5">
            <span class="badge {{ $dispute->network->value === 'visa' ? 'badge-blue' : 'badge-yellow' }}">
              {{ $dispute->network->label() }}
            </span>
          </td>
          <td class="px-4 py-3.5">
            <span class="text-sm font-semibold text-slate-800">
              {{ $dispute->transaction->currency }}
              {{ number_format($dispute->transaction->amount / 100, 2) }}
            </span>
          </td>
          <td class="px-4 py-3.5">
            <span class="{{ $dispute->status->badgeClass() }}">
              {{ $dispute->status->label() }}
            </span>
          </td>
          <td class="px-4 py-3.5">
            @if($dispute->response_document)
            <div class="flex items-center gap-1 text-emerald-600">
              <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              <span class="text-xs font-medium">Ready</span>
            </div>
            @else
            <span class="text-xs text-slate-400">Pending</span>
            @endif
          </td>
          <td class="px-4 py-3.5">
            <span class="text-xs text-slate-400">
              {{ $dispute->filed_at?->diffForHumans() ?? '—' }}
            </span>
          </td>
          <td class="px-4 py-3.5">
            <div class="flex items-center gap-2">
              <a href="{{ route('disputes.show', $dispute->ulid) }}"
                class="btn-secondary text-xs py-1 px-3">
                View
              </a>
              @if($dispute->response_document)
              <a href="{{ route('disputes.pdf', $dispute->ulid) }}"
                class="btn-primary text-xs py-1 px-3">
                PDF
              </a>
              @endif
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>

    @if($disputes->hasPages())
    <div class="px-6 py-4 border-t border-slate-100">
      {{ $disputes->links() }}
    </div>
    @endif
    @endif
  </div>
</div>
@endsection
