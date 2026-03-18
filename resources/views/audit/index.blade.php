@extends('layouts.app')
@section('title', 'Audit Log')
@section('page-title', 'Audit Log')

@section('content')
<div class="max-w-6xl mx-auto space-y-5">

  {{-- Header --}}
  <div class="flex items-center justify-between">
    <div>
      <p class="text-sm text-slate-500 mt-0.5">
        Complete record of every action in the system
      </p>
    </div>
    <span class="badge badge-slate">{{ number_format($totalCount) }} total events</span>
  </div>

  {{-- Filters --}}
  <form method="GET" action="{{ route('audit-log') }}" class="card p-4">
    <div class="flex items-center gap-3 flex-wrap">
      <div class="flex items-center gap-2">
        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
        </svg>
        <span class="text-sm font-medium text-slate-600">Filter:</span>
      </div>

      <select name="action"
        class="input-field py-1.5 text-xs w-auto"
        onchange="this.form.submit()">
        <option value="">All actions</option>
        @foreach($actions as $action)
        <option value="{{ $action }}"
          {{ request('action') === $action ? 'selected' : '' }}>
          {{ $action }}
        </option>
        @endforeach
      </select>

      <select name="resource"
        class="input-field py-1.5 text-xs w-auto"
        onchange="this.form.submit()">
        <option value="">All resources</option>
        @foreach($resources as $resource)
        <option value="{{ $resource }}"
          {{ request('resource') === $resource ? 'selected' : '' }}>
          {{ $resource }}
        </option>
        @endforeach
      </select>

      @if(request()->hasAny(['action', 'resource']))
      <a href="{{ route('audit-log') }}"
        class="text-xs text-slate-500 hover:text-slate-700 underline">
        Clear filters
      </a>
      @endif

      <span class="text-xs text-slate-400 ml-auto">
        {{ $logs->total() }} matching events
      </span>
    </div>
  </form>

  {{-- Audit timeline --}}
  <div class="card overflow-hidden">

    @if($logs->isEmpty())
    <div class="px-6 py-16 text-center">
      <div class="w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center mx-auto mb-3">
        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </div>
      <p class="text-sm font-medium text-slate-600">No audit events yet</p>
      <p class="text-xs text-slate-400 mt-1">
        Events are recorded automatically as you use the platform.
      </p>
    </div>
    @else
    <div class="divide-y divide-slate-50">
      @foreach($logs as $log)
      @php
      $actionConfig = [
      'transaction.intercepted' => ['bg' => '#eef2ff', 'color' => '#4f46e5', 'label' => 'Transaction'],
      'dispute.filed' => ['bg' => '#fef2f2', 'color' => '#dc2626', 'label' => 'Dispute'],
      'dispute.won' => ['bg' => '#ecfdf5', 'color' => '#059669', 'label' => 'Dispute Won'],
      'dispute.lost' => ['bg' => '#fef2f2', 'color' => '#dc2626', 'label' => 'Dispute Lost'],
      ];
      $ac = $actionConfig[$log->action] ?? ['bg' => '#f1f5f9', 'color' => '#64748b', 'label' => 'System'];
      @endphp
      <div class="px-6 py-4 hover:bg-slate-50 transition-colors"
        x-data="{ expanded: false }">
        <div class="flex items-start gap-4">

          {{-- Timeline dot --}}
          <div class="flex flex-col items-center shrink-0 mt-1">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center"
              style="background:{{ $ac['bg'] }};">
              <div class="w-2 h-2 rounded-full"
                style="background:{{ $ac['color'] }};"></div>
            </div>
            @if(!$loop->last)
            <div class="w-px flex-1 mt-1" style="background:#e2e8f0; min-height:16px;"></div>
            @endif
          </div>

          {{-- Content --}}
          <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-3">
              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                  {{-- Action --}}
                  <span class="text-xs font-semibold px-2 py-0.5 rounded-md"
                    style="background:{{ $ac['bg'] }};color:{{ $ac['color'] }};">
                    {{ $log->action }}
                  </span>

                  {{-- Actor --}}
                  <span class="badge badge-slate">
                    {{ $log->actor_type?->label() ?? 'system' }}
                  </span>

                  {{-- Resource --}}
                  @if($log->resource_type && $log->resource_id)
                  <span class="text-xs text-slate-500">
                    {{ $log->resource_type }}:
                    <span class="font-mono">{{ substr($log->resource_id, 0, 16) }}...</span>
                  </span>
                  @endif
                </div>

                {{-- After state summary --}}
                @if($log->after_state)
                <div class="mt-1.5 flex items-center gap-3 flex-wrap">
                  @foreach($log->after_state as $key => $value)
                  @if(!is_array($value))
                  <span class="text-xs text-slate-500">
                    <span class="text-slate-400">{{ $key }}:</span>
                    <span class="font-medium text-slate-700">{{ $value }}</span>
                  </span>
                  @endif
                  @endforeach
                </div>
                @endif
              </div>

              <div class="text-right shrink-0">
                <p class="text-xs text-slate-500">
                  {{ $log->created_at->format('M d, Y') }}
                </p>
                <p class="text-xs font-mono text-slate-400">
                  {{ $log->created_at->format('H:i:s') }}
                </p>
                @if($log->ip_address)
                <p class="text-xs text-slate-300 font-mono mt-0.5">
                  {{ $log->ip_address }}
                </p>
                @endif
              </div>
            </div>

            {{-- Expandable state diff --}}
            @if($log->before_state || $log->after_state)
            <button @click="expanded = !expanded"
              class="mt-2 text-xs text-slate-400 hover:text-slate-600 flex items-center gap-1">
              <svg class="w-3 h-3 transition-transform"
                :class="expanded ? 'rotate-90' : ''"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
              <span x-text="expanded ? 'Hide state' : 'Show state'"></span>
            </button>

            <div x-show="expanded" x-cloak class="mt-2 grid grid-cols-2 gap-3">
              @if($log->before_state)
              <div>
                <p class="text-xs font-medium text-slate-500 mb-1">Before</p>
                <pre class="text-xs font-mono bg-slate-50 border border-slate-200 rounded-lg p-2 overflow-x-auto text-slate-600">{{ json_encode($log->before_state, JSON_PRETTY_PRINT) }}</pre>
              </div>
              @endif
              @if($log->after_state)
              <div>
                <p class="text-xs font-medium text-slate-500 mb-1">After</p>
                <pre class="text-xs font-mono bg-emerald-50 border border-emerald-200 rounded-lg p-2 overflow-x-auto text-slate-600">{{ json_encode($log->after_state, JSON_PRETTY_PRINT) }}</pre>
              </div>
              @endif
            </div>
            @endif
          </div>
        </div>
      </div>
      @endforeach
    </div>

    {{-- Pagination --}}
    @if($logs->hasPages())
    <div class="px-6 py-4 border-t border-slate-100">
      {{ $logs->links() }}
    </div>
    @endif
    @endif
  </div>

</div>
@endsection
