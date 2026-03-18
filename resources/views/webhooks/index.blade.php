@extends('layouts.app')
@section('title', 'Webhooks')
@section('page-title', 'Webhook Delivery Log')

@section('content')
<div class="max-w-7xl mx-auto space-y-5">

  {{-- Stats row --}}
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    @php
    $wStats = [
    ['label' => 'Total Sent', 'value' => $stats['total'], 'color' => '#4f46e5', 'bg' => '#eef2ff'],
    ['label' => 'Delivered', 'value' => $stats['delivered'], 'color' => '#059669', 'bg' => '#ecfdf5'],
    ['label' => 'Failed', 'value' => $stats['failed'], 'color' => '#dc2626', 'bg' => '#fef2f2'],
    ['label' => 'Retrying', 'value' => $stats['retrying'], 'color' => '#d97706', 'bg' => '#fffbeb'],
    ];
    @endphp
    @foreach($wStats as $ws)
    <div class="card p-5 flex items-center gap-4">
      <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
        style="background-color: {{ $ws['bg'] }}">
        <span class="text-base font-bold" style="color: {{ $ws['color'] }}">
          {{ $ws['value'] }}
        </span>
      </div>
      <p class="text-sm font-medium text-slate-600">{{ $ws['label'] }}</p>
    </div>
    @endforeach
  </div>

  {{-- Delivery log table --}}
  <div class="card overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
      <h3 class="text-sm font-semibold text-slate-800">Delivery Log</h3>
      <p class="text-xs text-slate-400">All webhook delivery attempts</p>
    </div>

    @if($deliveries->isEmpty())
    <div class="px-6 py-16 text-center">
      <div class="w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center mx-auto mb-3">
        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
      </div>
      <p class="text-sm font-medium text-slate-600">No webhook deliveries yet</p>
      <p class="text-xs text-slate-400 mt-1">
        Webhooks fire automatically when transactions are scored or disputes are filed.
      </p>
    </div>
    @else
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead>
          <tr class="border-b border-slate-100">
            <th class="text-left text-xs font-medium text-slate-400 uppercase tracking-wider px-6 py-3">Event</th>
            <th class="text-left text-xs font-medium text-slate-400 uppercase tracking-wider px-4 py-3">Endpoint</th>
            <th class="text-left text-xs font-medium text-slate-400 uppercase tracking-wider px-4 py-3">Status</th>
            <th class="text-left text-xs font-medium text-slate-400 uppercase tracking-wider px-4 py-3">HTTP</th>
            <th class="text-left text-xs font-medium text-slate-400 uppercase tracking-wider px-4 py-3">Attempts</th>
            <th class="text-left text-xs font-medium text-slate-400 uppercase tracking-wider px-4 py-3">Sent</th>
            <th class="px-4 py-3"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
          @foreach($deliveries as $delivery)
          <tr class="hover:bg-slate-50 transition-colors"
            x-data="{ expanded: false }">
            <td class="px-6 py-3.5">
              <p class="text-xs font-medium text-slate-700">
                {{ $delivery->event_type->label() }}
              </p>
              <p class="text-xs font-mono text-slate-400 mt-0.5">
                {{ substr($delivery->ulid, 0, 16) }}...
              </p>
            </td>
            <td class="px-4 py-3.5">
              <p class="text-xs font-mono text-slate-500 max-w-[180px] truncate">
                {{ $delivery->url }}
              </p>
            </td>
            <td class="px-4 py-3.5">
              <span class="{{ $delivery->status->badgeClass() }}">
                {{ $delivery->status->label() }}
              </span>
            </td>
            <td class="px-4 py-3.5">
              @if($delivery->http_status)
              @php
              $httpColor = $delivery->http_status >= 200 && $delivery->http_status < 300
                ? 'text-emerald-600' : 'text-red-600' ;
                @endphp
                <span class="text-sm font-mono font-bold {{ $httpColor }}">
                {{ $delivery->http_status }}
                </span>
                @else
                <span class="text-xs text-slate-400">—</span>
                @endif
            </td>
            <td class="px-4 py-3.5">
              <div class="flex items-center gap-1">
                @for($i = 1; $i <= 3; $i++)
                  <div class="w-2 h-2 rounded-full
                                            {{ $i <= $delivery->attempt_number
                                                ? ($delivery->status->value === 'delivered' ? 'bg-emerald-400' : 'bg-red-400')
                                                : 'bg-slate-200' }}">
              </div>
              @endfor
              <span class="text-xs text-slate-400 ml-1">
                {{ $delivery->attempt_number }}/3
              </span>
    </div>
    </td>
    <td class="px-4 py-3.5">
      <span class="text-xs text-slate-400">
        {{ $delivery->created_at->diffForHumans() }}
      </span>
    </td>
    <td class="px-4 py-3.5">
      <div class="flex items-center gap-2">
        {{-- Expand response body --}}
        @if($delivery->response_body)
        <button @click="expanded = !expanded"
          class="text-xs text-slate-500 hover:text-slate-700 underline">
          <span x-text="expanded ? 'Hide' : 'Response'"></span>
        </button>
        @endif

        {{-- Re-trigger for failed --}}
        @if($delivery->status->value === 'failed')
        <form method="POST"
          action="{{ route('webhooks.retrigger', $delivery->ulid) }}">
          @csrf
          <button type="submit"
            class="text-xs font-medium text-indigo-600 hover:text-indigo-500">
            Retry
          </button>
        </form>
        @endif
      </div>
    </td>
    </tr>

    {{-- Expandable response body row --}}
    @if($delivery->response_body)
    <tr x-show="expanded" x-cloak
      class="bg-slate-50">
      <td colspan="7" class="px-6 py-3">
        <p class="text-xs font-medium text-slate-500 mb-1">Response body:</p>
        <pre class="text-xs font-mono text-slate-600 bg-white border border-slate-200 rounded-lg p-3 overflow-x-auto">{{ $delivery->response_body }}</pre>
      </td>
    </tr>
    @endif

    @endforeach
    </tbody>
    </table>
  </div>

  @if($deliveries->hasPages())
  <div class="px-6 py-4 border-t border-slate-100">
    {{ $deliveries->links() }}
  </div>
  @endif
  @endif
</div>

</div>
@endsection
