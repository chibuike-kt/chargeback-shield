@extends('layouts.app')
@section('title', 'Simulation Panel')
@section('page-title', 'Simulation Panel')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

  {{-- Header --}}
  <div class="card p-6" style="background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%); border:0;">
    <div class="flex items-start justify-between">
      <div>
        <h2 class="text-lg font-bold text-white">Demo Simulation Panel</h2>
        <p class="text-sm mt-1" style="color:#c7d2fe;">
          Run end-to-end scenarios that produce real records, real scores,
          real evidence bundles, and real webhooks. Watch the live feed as each scenario executes.
        </p>
      </div>
      <div class="flex items-center gap-2 px-3 py-1.5 rounded-full border"
        style="background:rgba(255,255,255,0.1); border-color:rgba(255,255,255,0.2);">
        <span class="relative flex h-2 w-2">
          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
          <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
        </span>
        <span class="text-xs font-medium text-white">Live mode</span>
      </div>
    </div>
  </div>

  {{-- Scenarios grid --}}
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-4" x-data="simulationPanel()">

    {{-- Scenario cards --}}
    @php
    $scenarios = [
    [
    'id' => 'normal_transaction',
    'title' => 'Normal Transaction',
    'description' => 'A clean low-risk transaction from a known Nigerian card. Approved with evidence locked.',
    'outcome' => 'Approved',
    'color' => 'emerald',
    'bg' => '#ecfdf5',
    'border' => '#6ee7b7',
    'text' => '#059669',
    'icon' => '
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />',
    ],
    [
    'id' => 'card_testing_attack',
    'title' => 'Card Testing Attack',
    'description' => '8 rapid micro-transactions on the same card. Velocity windows fill up, card gets blocked.',
    'outcome' => 'Declined',
    'color' => 'red',
    'bg' => '#fef2f2',
    'border' => '#fca5a5',
    'text' => '#dc2626',
    'icon' => '
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13 10V3L4 14h7v7l9-11h-7z" />',
    ],
    [
    'id' => 'account_takeover',
    'title' => 'Account Takeover Attempt',
    'description' => 'New device, Russian IP, Nigerian card, brand new session, high amount. All signals fire.',
    'outcome' => 'Declined',
    'color' => 'red',
    'bg' => '#fef2f2',
    'border' => '#fca5a5',
    'text' => '#dc2626',
    'icon' => '
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />',
    ],
    [
    'id' => 'high_value_stepup',
    'title' => 'High-Value Step-Up',
    'description' => 'Legitimate high-value transaction from a known device. Clean signals but amount triggers 3DS.',
    'outcome' => 'Step-Up',
    'color' => 'amber',
    'bg' => '#fffbeb',
    'border' => '#fcd34d',
    'text' => '#d97706',
    'icon' => '
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />',
    ],
    [
    'id' => 'chargeback_filed',
    'title' => 'Chargeback Filed',
    'description' => 'Approved transaction gets a chargeback. Evidence bundle retrieved and response auto-generated.',
    'outcome' => 'Responded',
    'color' => 'indigo',
    'bg' => '#eef2ff',
    'border' => '#a5b4fc',
    'text' => '#4f46e5',
    'icon' => '
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />',
    ],
    [
    'id' => 'webhook_failure',
    'title' => 'Webhook Failure & Retry',
    'description' => 'Webhook endpoint returns 500. Retry cycle kicks in with exponential backoff.',
    'outcome' => 'Retrying',
    'color' => 'slate',
    'bg' => '#f8fafc',
    'border' => '#cbd5e1',
    'text' => '#64748b',
    'icon' => '
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />',
    ],
    ];
    @endphp

    @foreach($scenarios as $scenario)
    <div class="card p-5 transition-all duration-200"
      :class="running === '{{ $scenario['id'] }}' ? 'ring-2 ring-indigo-400' : ''"
      style="border-color: {{ $scenario['border'] }};">

      <div class="flex items-start justify-between mb-3">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
            style="background:{{ $scenario['bg'] }};">
            <svg class="w-5 h-5" fill="none" stroke="{{ $scenario['text'] }}" viewBox="0 0 24 24">
              {!! $scenario['icon'] !!}
            </svg>
          </div>
          <div>
            <h3 class="text-sm font-bold text-slate-800">{{ $scenario['title'] }}</h3>
            <span class="text-xs font-medium px-2 py-0.5 rounded-full"
              style="background:{{ $scenario['bg'] }};color:{{ $scenario['text'] }};">
              {{ $scenario['outcome'] }}
            </span>
          </div>
        </div>
      </div>

      <p class="text-xs text-slate-500 mb-4">{{ $scenario['description'] }}</p>

      {{-- Run button --}}
      <button
        @click="runScenario('{{ $scenario['id'] }}')"
        :disabled="running !== null"
        class="w-full py-2 px-4 rounded-lg text-sm font-semibold transition-all duration-150"
        :class="running === '{{ $scenario['id'] }}'
                    ? 'bg-indigo-100 text-indigo-600 cursor-wait'
                    : running !== null
                        ? 'bg-slate-100 text-slate-400 cursor-not-allowed'
                        : 'bg-indigo-600 text-white hover:bg-indigo-700'">
        <span x-show="running !== '{{ $scenario['id'] }}'">
          Run Scenario
        </span>
        <span x-show="running === '{{ $scenario['id'] }}'" class="flex items-center justify-center gap-2">
          <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
          </svg>
          Running...
        </span>
      </button>
    </div>
    @endforeach

    {{-- Results panel --}}
    <div class="card lg:col-span-2 overflow-hidden"
      x-show="result !== null">

      <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
        <div class="flex items-center gap-3">
          <h3 class="text-sm font-semibold text-slate-800" x-text="result?.scenario"></h3>
          <span class="badge badge-green" x-show="result?.decision === 'allow'">Approved</span>
          <span class="badge badge-yellow" x-show="result?.decision === 'step_up'">Step-Up</span>
          <span class="badge badge-red" x-show="result?.decision === 'decline'">Declined</span>
          <span class="badge badge-blue" x-show="result?.dispute_id">Responded</span>
        </div>
        <button @click="result = null" class="text-slate-400 hover:text-slate-600">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <div class="p-5 grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Steps --}}
        <div>
          <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">
            Execution Steps
          </h4>
          <div class="space-y-2">
            <template x-for="(step, i) in result?.steps" :key="i">
              <div class="flex items-start gap-3">
                <div class="w-5 h-5 rounded-full flex items-center justify-center shrink-0 mt-0.5"
                  :style="stepStyle(step.status)">
                  <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"
                    x-show="step.status === 'success'">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                  <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"
                    x-show="step.status === 'danger'">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                  </svg>
                  <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"
                    x-show="step.status === 'warning' || step.status === 'pending'">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div>
                  <p class="text-xs text-slate-700" x-text="step.message"></p>
                  <p class="text-xs text-slate-400" x-text="step.timestamp"></p>
                </div>
              </div>
            </template>
          </div>
        </div>

        {{-- Record links --}}
        <div>
          <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">
            Created Records
          </h4>
          <div class="space-y-3">

            <div x-show="result?.transaction_id" class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
              <div>
                <p class="text-xs font-medium text-slate-600">Transaction</p>
                <p class="text-xs font-mono text-slate-400 mt-0.5" x-text="result?.transaction_id?.substring(0, 20) + '...'"></p>
              </div>
              <div class="text-right">
                <p class="text-xs font-mono font-bold"
                  :style="scoreColor(result?.risk_score)"
                  x-text="'Score: ' + result?.risk_score?.toFixed(3)">
                </p>
              </div>
            </div>

            <div x-show="result?.evidence_id" class="flex items-center justify-between p-3 rounded-lg"
              style="background:#ecfdf5;">
              <div>
                <p class="text-xs font-medium" style="color:#065f46;">Evidence Bundle</p>
                <p class="text-xs font-mono mt-0.5" style="color:#6ee7b7;" x-text="result?.evidence_id?.substring(0, 20) + '...'"></p>
              </div>
              <div class="flex items-center gap-1" style="color:#059669;">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span class="text-xs font-medium">Locked</span>
              </div>
            </div>

            <div x-show="result?.dispute_id" class="flex items-center justify-between p-3 rounded-lg"
              style="background:#eef2ff;">
              <div>
                <p class="text-xs font-medium" style="color:#3730a3;">Dispute Response</p>
                <p class="text-xs font-mono mt-0.5" style="color:#a5b4fc;" x-text="result?.dispute_id?.substring(0, 20) + '...'"></p>
              </div>
              <a :href="'/disputes/' + result?.dispute_id"
                class="text-xs font-medium px-2 py-1 rounded-lg"
                style="background:#4f46e5;color:white;">
                View →
              </a>
            </div>

            <div class="p-3 bg-slate-50 rounded-lg">
              <p class="text-xs font-medium text-slate-600 mb-1">Quick links</p>
              <div class="flex flex-wrap gap-2">
                <a href="{{ route('dashboard') }}" class="text-xs text-indigo-600 hover:underline">Dashboard</a>
                <span class="text-slate-300">·</span>
                <a href="{{ route('webhooks') }}" class="text-xs text-indigo-600 hover:underline">Webhook Log</a>
                <span class="text-slate-300">·</span>
                <a href="{{ route('disputes') }}" class="text-xs text-indigo-600 hover:underline">Disputes</a>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection

@push('scripts')
<script>
  function simulationPanel() {
    return {
      running: null,
      result: null,

      runScenario(scenarioId) {
        if (this.running) return;

        this.running = scenarioId;
        this.result = null;

        fetch('/app/simulate/run', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
              'Accept': 'application/json',
            },
            body: JSON.stringify({
              scenario: scenarioId
            }),
          })
          .then(r => r.json())
          .then(data => {
            this.result = data;
            this.running = null;
          })
          .catch(err => {
            console.error('Simulation error:', err);
            this.running = null;
          });
      },

      stepStyle(status) {
        const styles = {
          success: 'background:#059669;color:white;',
          danger: 'background:#dc2626;color:white;',
          warning: 'background:#d97706;color:white;',
          pending: 'background:#94a3b8;color:white;',
        };
        return styles[status] || styles.pending;
      },

      scoreColor(score) {
        if (!score) return 'color:#64748b;';
        if (score < 0.4) return 'color:#059669;';
        if (score < 0.7) return 'color:#d97706;';
        return 'color:#dc2626;';
      },
    };
  }
</script>
@endpush
