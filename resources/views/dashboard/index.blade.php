@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- Welcome banner --}}
    <div class="rounded-2xl p-6 text-white relative overflow-hidden"
        style="background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);">
        <div class="relative z-10 flex items-start justify-between">
            <div>
                <h2 class="text-lg font-bold">
                    Welcome back, {{ $merchant->company_name }}
                </h2>
                <p class="text-indigo-200 text-sm mt-1 max-w-lg">
                    Your real-time chargeback protection layer is active.
                    Every transaction is being intercepted, scored, and locked.
                </p>
            </div>
            <div class="flex gap-2 shrink-0">
                <a href="{{ route('simulate') }}"
                    class="px-4 py-2 bg-white text-indigo-700 text-sm font-semibold rounded-lg hover:bg-indigo-50 transition-colors">
                    Run simulation
                </a>
            </div>
        </div>
        {{-- Decorative background circles --}}
        <div class="absolute -right-8 -top-8 w-48 h-48 rounded-full bg-white opacity-5"></div>
        <div class="absolute -right-4 -bottom-12 w-64 h-64 rounded-full bg-white opacity-5"></div>
    </div>

    {{-- Stats row --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        @php
            $stats = [
                [
                    'label'  => 'Total Transactions',
                    'value'  => number_format($totalTransactions),
                    'sub'    => $totalDeclined . ' declined',
                    'color'  => 'indigo',
                    'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13 10V3L4 14h7v7l9-11h-7z"/>',
                ],
                [
                    'label'  => 'Flagged (Step-Up)',
                    'value'  => number_format($totalFlagged),
                    'sub'    => $totalTransactions > 0 ? round(($totalFlagged / $totalTransactions) * 100, 1) . '% of total' : 'No data',
                    'color'  => 'amber',
                    'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>',
                ],
                [
                    'label'  => 'Chargebacks Filed',
                    'value'  => number_format($totalChargebacks),
                    'sub'    => $totalDisputesOpen . ' open',
                    'color'  => 'red',
                    'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>',
                ],
                [
                    'label'  => 'Disputes Won',
                    'value'  => number_format($totalDisputesWon),
                    'sub'    => $totalChargebacks > 0 ? round(($totalDisputesWon / $totalChargebacks) * 100) . '% win rate' : 'No disputes yet',
                    'color'  => 'emerald',
                    'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>',
                ],
            ];
        @endphp

        @foreach($stats as $stat)
        @php
            $colorMap = [
                'indigo'  => ['bg' => '#eef2ff', 'icon' => '#4f46e5', 'text' => '#4338ca'],
                'amber'   => ['bg' => '#fffbeb', 'icon' => '#d97706', 'text' => '#b45309'],
                'red'     => ['bg' => '#fef2f2', 'icon' => '#dc2626', 'text' => '#b91c1c'],
                'emerald' => ['bg' => '#ecfdf5', 'icon' => '#059669', 'text' => '#047857'],
            ];
            $c = $colorMap[$stat['color']];
        @endphp
        <div class="card p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">
                        {{ $stat['label'] }}
                    </p>
                    <p class="text-3xl font-bold text-slate-800 mt-1">{{ $stat['value'] }}</p>
                    <p class="text-xs text-slate-400 mt-1">{{ $stat['sub'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                    style="background-color: {{ $c['bg'] }}">
                    <svg class="w-5 h-5" fill="none" stroke="{{ $c['icon'] }}" viewBox="0 0 24 24">
                        {!! $stat['icon'] !!}
                    </svg>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Charts row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Transaction volume chart --}}
        <div class="card p-5 lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-semibold text-slate-800">Transaction Volume</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Last 7 days</p>
                </div>
                <span class="badge badge-blue">Daily</span>
            </div>
            @if($totalTransactions > 0)
                <canvas id="volumeChart" height="110"></canvas>
            @else
                <div class="flex flex-col items-center justify-center h-28 text-center">
                    <p class="text-sm text-slate-400">No transaction data yet</p>
                    <p class="text-xs text-slate-300 mt-1">Run a simulation to populate this chart</p>
                </div>
            @endif
        </div>

        {{-- Risk distribution chart --}}
        <div class="card p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-semibold text-slate-800">Risk Distribution</h3>
                    <p class="text-xs text-slate-400 mt-0.5">All time</p>
                </div>
            </div>
            @if($totalTransactions > 0)
                <canvas id="riskChart" height="160"></canvas>
                <div class="mt-4 space-y-2">
                    @foreach([
                        ['label' => 'Low Risk',    'key' => 'low',    'color' => '#059669'],
                        ['label' => 'Medium Risk',  'key' => 'medium', 'color' => '#d97706'],
                        ['label' => 'High Risk',    'key' => 'high',   'color' => '#dc2626'],
                    ] as $item)
                    <div class="flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 rounded-full"
                                style="background-color: {{ $item['color'] }}"></div>
                            <span class="text-slate-600">{{ $item['label'] }}</span>
                        </div>
                        <span class="font-medium text-slate-700">
                            {{ $riskChartData[$item['key']] }}
                        </span>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center h-36 text-center">
                    <p class="text-sm text-slate-400">No data yet</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Bottom row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Recent transactions table --}}
        <div class="card lg:col-span-2">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <h3 class="text-sm font-semibold text-slate-800">Recent Transactions</h3>
                <a href="{{ route('transactions') }}"
                    class="text-xs font-medium text-indigo-600 hover:text-indigo-500">
                    View all →
                </a>
            </div>

            @if($recentTransactions->isEmpty())
                <div class="px-5 py-12 text-center">
                    <div class="w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-slate-600">No transactions yet</p>
                    <p class="text-xs text-slate-400 mt-1">
                        Integrate the API or run a simulation to see transactions here.
                    </p>
                    <a href="{{ route('simulate') }}" class="btn-primary mt-4 text-xs">
                        Run simulation
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-slate-100">
                                <th class="text-left text-xs font-medium text-slate-400 uppercase tracking-wider px-5 py-3">
                                    Transaction
                                </th>
                                <th class="text-left text-xs font-medium text-slate-400 uppercase tracking-wider px-3 py-3">
                                    Amount
                                </th>
                                <th class="text-left text-xs font-medium text-slate-400 uppercase tracking-wider px-3 py-3">
                                    Risk Score
                                </th>
                                <th class="text-left text-xs font-medium text-slate-400 uppercase tracking-wider px-3 py-3">
                                    Decision
                                </th>
                                <th class="text-left text-xs font-medium text-slate-400 uppercase tracking-wider px-3 py-3">
                                    Evidence
                                </th>
                                <th class="text-left text-xs font-medium text-slate-400 uppercase tracking-wider px-3 py-3">
                                    Time
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($recentTransactions as $tx)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-2.5">
                                        @php
                                            $decisionColors = [
                                                'allow'    => ['bg' => '#ecfdf5', 'dot' => '#059669'],
                                                'step_up'  => ['bg' => '#fffbeb', 'dot' => '#d97706'],
                                                'decline'  => ['bg' => '#fef2f2', 'dot' => '#dc2626'],
                                            ];
                                            $dc = $decisionColors[$tx->decision->value] ?? $decisionColors['allow'];
                                        @endphp
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                                            style="background-color: {{ $dc['bg'] }}">
                                            <div class="w-2 h-2 rounded-full"
                                                style="background-color: {{ $dc['dot'] }}"></div>
                                        </div>
                                        <div>
                                            <p class="text-xs font-mono font-medium text-slate-700">
                                                ****{{ $tx->card_last4 }}
                                            </p>
                                            <p class="text-xs text-slate-400">
                                                BIN {{ $tx->card_bin }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-3">
                                    <p class="text-sm font-semibold text-slate-800">
                                        {{ $tx->currency }} {{ number_format($tx->amount / 100, 2) }}
                                    </p>
                                </td>
                                <td class="px-3 py-3">
                                    @php
                                        $score = $tx->risk_score;
                                        $scoreColor = $score < 0.4 ? '#059669' : ($score < 0.7 ? '#d97706' : '#dc2626');
                                    @endphp
                                    <div class="flex items-center gap-2">
                                        <div class="w-16 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full"
                                                style="width: {{ $score * 100 }}%; background-color: {{ $scoreColor }}">
                                            </div>
                                        </div>
                                        <span class="text-xs font-medium" style="color: {{ $scoreColor }}">
                                            {{ number_format($score, 2) }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-3 py-3">
                                    <span class="{{ $tx->decision->badgeClass() }}">
                                        {{ $tx->decision->label() }}
                                    </span>
                                </td>
                                <td class="px-3 py-3">
                                    @if($tx->evidenceBundle)
                                        <div class="flex items-center gap-1 text-emerald-600">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 1l2.928 6.029L20 8.236l-5 4.897 1.18 6.867L10 16.9l-6.18 3.1L5 13.133 0 8.236l7.072-1.207L10 1z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-xs font-medium">Locked</span>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3">
                                    <span class="text-xs text-slate-400">
                                        {{ $tx->created_at->diffForHumans() }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Right column --}}
        <div class="space-y-4">

            {{-- Webhook health --}}
            <div class="card p-5">
                <h3 class="text-sm font-semibold text-slate-800 mb-4">Webhook Health</h3>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-slate-500">Delivery rate</span>
                    <span class="text-sm font-bold {{ $webhookHealthPct >= 90 ? 'text-emerald-600' : ($webhookHealthPct >= 70 ? 'text-amber-600' : 'text-red-600') }}">
                        {{ $webhookHealthPct }}%
                    </span>
                </div>
                <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden mb-3">
                    <div class="h-full rounded-full transition-all duration-500
                        {{ $webhookHealthPct >= 90 ? 'bg-emerald-500' : ($webhookHealthPct >= 70 ? 'bg-amber-500' : 'bg-red-500') }}"
                        style="width: {{ $webhookHealthPct }}%">
                    </div>
                </div>
                <div class="flex justify-between text-xs text-slate-400">
                    <span>{{ $webhookDelivered }} delivered</span>
                    <span>{{ $webhookFailed }} failed</span>
                </div>
            </div>

            {{-- API credentials --}}
            <div class="card p-5">
                <h3 class="text-sm font-semibold text-slate-800 mb-4">API Credentials</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-slate-400 mb-1">API Key</p>
                        <div class="flex items-center gap-2 bg-slate-50 rounded-lg px-3 py-2"
                            x-data="{ copied: false }"
                            @click="navigator.clipboard.writeText('{{ auth('merchant')->user()->api_key }}'); copied = true; setTimeout(() => copied = false, 2000)"
                            style="cursor: pointer" title="Click to copy">
                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-xs font-mono text-slate-600 truncate"
                                x-text="copied ? 'Copied!' : '{{ substr(auth('merchant')->user()->api_key, 0, 24) }}...'">
                            </span>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 mb-1">Webhook Secret</p>
                        <div class="flex items-center gap-2 bg-slate-50 rounded-lg px-3 py-2"
                            x-data="{ show: false }">
                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                            <span class="text-xs font-mono text-slate-600 truncate flex-1"
                                x-text="show ? '{{ auth('merchant')->user()->webhook_secret }}' : '••••••••••••••••••••••••'">
                            </span>
                            <button @click="show = !show" class="text-slate-400 hover:text-slate-600 shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        x-show="!show"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        x-show="show"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 mb-1">Webhook URL</p>
                        <div class="bg-slate-50 rounded-lg px-3 py-2">
                            <span class="text-xs font-mono text-slate-500 truncate block">
                                {{ auth('merchant')->user()->webhook_url ?? 'Not configured' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick actions --}}
            <div class="card p-5">
                <h3 class="text-sm font-semibold text-slate-800 mb-3">Quick Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('simulate') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-50 transition-colors group">
                        <div class="w-7 h-7 bg-indigo-50 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="text-sm text-slate-600 group-hover:text-slate-800">Run simulation</span>
                    </a>
                    <a href="{{ route('transactions') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-50 transition-colors group">
                        <div class="w-7 h-7 bg-blue-50 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <span class="text-sm text-slate-600 group-hover:text-slate-800">View transactions</span>
                    </a>
                    <a href="{{ route('disputes') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-50 transition-colors group">
                        <div class="w-7 h-7 bg-red-50 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <span class="text-sm text-slate-600 group-hover:text-slate-800">Manage disputes</span>
                    </a>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    @if($totalTransactions > 0)

    // ── Volume chart ──────────────────────────────────────────────────────────
    const volumeCtx = document.getElementById('volumeChart');
    if (volumeCtx) {
        new Chart(volumeCtx, {
            type: 'bar',
            data: {
                labels: @json($volumeLabels),
                datasets: [{
                    label: 'Transactions',
                    data: @json($volumeCounts),
                    backgroundColor: 'rgba(99, 102, 241, 0.15)',
                    borderColor: 'rgba(99, 102, 241, 0.8)',
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleColor: '#94a3b8',
                        bodyColor: '#f1f5f9',
                        padding: 10,
                        cornerRadius: 8,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            color: '#94a3b8',
                            font: { size: 11, family: 'Plus Jakarta Sans' }
                        },
                        grid: { color: '#f1f5f9' }
                    },
                    x: {
                        ticks: {
                            color: '#94a3b8',
                            font: { size: 11, family: 'Plus Jakarta Sans' }
                        },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // ── Risk distribution doughnut ────────────────────────────────────────────
    const riskCtx = document.getElementById('riskChart');
    if (riskCtx) {
        new Chart(riskCtx, {
            type: 'doughnut',
            data: {
                labels: ['Low Risk', 'Medium Risk', 'High Risk'],
                datasets: [{
                    data: [
                        {{ $riskChartData['low'] }},
                        {{ $riskChartData['medium'] }},
                        {{ $riskChartData['high'] }}
                    ],
                    backgroundColor: ['#059669', '#d97706', '#dc2626'],
                    borderWidth: 0,
                    hoverOffset: 4,
                }]
            },
            options: {
                responsive: true,
                cutout: '72%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleColor: '#94a3b8',
                        bodyColor: '#f1f5f9',
                        padding: 10,
                        cornerRadius: 8,
                    }
                }
            }
        });
    }

    @endif
});
</script>
@endpush
