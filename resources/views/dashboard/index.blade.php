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
                <p class="text-sm mt-1 max-w-lg" style="color: #c7d2fe;">
                    Your real-time chargeback protection layer is active.
                    Every transaction is being intercepted, scored, and locked.
                </p>
            </div>
            <div class="flex gap-2 shrink-0">
                <a href="{{ route('simulate') }}"
                    class="px-4 py-2 bg-white text-sm font-semibold rounded-lg hover:bg-indigo-50 transition-colors"
                    style="color: #4338ca;">
                    Run simulation
                </a>
            </div>
        </div>
        <div class="absolute -right-8 -top-8 w-48 h-48 rounded-full bg-white" style="opacity:0.05;"></div>
        <div class="absolute -right-4 -bottom-12 w-64 h-64 rounded-full bg-white" style="opacity:0.05;"></div>
    </div>

    {{-- Stats row --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        <div class="card p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total Transactions</p>
                    <p class="text-3xl font-bold text-slate-800 mt-1">{{ number_format($totalTransactions) }}</p>
                    <p class="text-xs text-slate-400 mt-1">{{ $totalDeclined }} declined</p>
                </div>
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background:#eef2ff;">
                    <svg class="w-5 h-5" fill="none" stroke="#4f46e5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Flagged (Step-Up)</p>
                    <p class="text-3xl font-bold text-slate-800 mt-1">{{ number_format($totalFlagged) }}</p>
                    <p class="text-xs text-slate-400 mt-1">
                        {{ $totalTransactions > 0 ? round(($totalFlagged / $totalTransactions) * 100, 1) : 0 }}% of total
                    </p>
                </div>
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background:#fffbeb;">
                    <svg class="w-5 h-5" fill="none" stroke="#d97706" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Chargebacks Filed</p>
                    <p class="text-3xl font-bold text-slate-800 mt-1">{{ number_format($totalChargebacks) }}</p>
                    <p class="text-xs text-slate-400 mt-1">{{ $totalDisputesOpen }} open</p>
                </div>
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background:#fef2f2;">
                    <svg class="w-5 h-5" fill="none" stroke="#dc2626" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Disputes Won</p>
                    <p class="text-3xl font-bold text-slate-800 mt-1">{{ number_format($totalDisputesWon) }}</p>
                    <p class="text-xs text-slate-400 mt-1">
                        {{ $totalChargebacks > 0 ? round(($totalDisputesWon / $totalChargebacks) * 100) : 0 }}% win rate
                    </p>
                </div>
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background:#ecfdf5;">
                    <svg class="w-5 h-5" fill="none" stroke="#059669" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                </div>
            </div>
        </div>

    </div>

    {{-- Charts row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        <div class="card p-5 lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-semibold text-slate-800">Transaction Volume</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Last 7 days</p>
                </div>
                <span class="badge badge-blue">Daily</span>
            </div>
            <canvas id="volumeChart" height="110"></canvas>
        </div>

        <div class="card p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-semibold text-slate-800">Risk Distribution</h3>
                    <p class="text-xs text-slate-400 mt-0.5">All time</p>
                </div>
            </div>
            <canvas id="riskChart" height="160"></canvas>
            <div class="mt-4 space-y-2">
                <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full" style="background:#059669;"></div>
                        <span class="text-slate-600">Low Risk</span>
                    </div>
                    <span class="font-medium text-slate-700">{{ $riskChartData['low'] }}</span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full" style="background:#d97706;"></div>
                        <span class="text-slate-600">Medium Risk</span>
                    </div>
                    <span class="font-medium text-slate-700">{{ $riskChartData['medium'] }}</span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full" style="background:#dc2626;"></div>
                        <span class="text-slate-600">High Risk</span>
                    </div>
                    <span class="font-medium text-slate-700">{{ $riskChartData['high'] }}</span>
                </div>
            </div>
        </div>

    </div>

    {{-- Bottom row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Recent transactions --}}
        <div class="card lg:col-span-2">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <h3 class="text-sm font-semibold text-slate-800">Recent Transactions</h3>
                <a href="{{ route('transactions') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-500">
                    View all →
                </a>
            </div>

            @if($recentTransactions->isEmpty())
            <div class="px-5 py-12 text-center">
                <div class="w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <p class="text-sm font-medium text-slate-600">No transactions yet</p>
                <p class="text-xs text-slate-400 mt-1">Integrate the API or run a simulation.</p>
                <a href="{{ route('simulate') }}" class="btn-primary mt-4 text-xs">Run simulation</a>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-100">
                            <th class="text-left text-xs font-medium text-slate-400 uppercase tracking-wider px-5 py-3">Card</th>
                            <th class="text-left text-xs font-medium text-slate-400 uppercase tracking-wider px-3 py-3">Amount</th>
                            <th class="text-left text-xs font-medium text-slate-400 uppercase tracking-wider px-3 py-3">Risk</th>
                            <th class="text-left text-xs font-medium text-slate-400 uppercase tracking-wider px-3 py-3">Decision</th>
                            <th class="text-left text-xs font-medium text-slate-400 uppercase tracking-wider px-3 py-3">Evidence</th>
                            <th class="text-left text-xs font-medium text-slate-400 uppercase tracking-wider px-3 py-3">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($recentTransactions as $tx)
                        @php
                        $dotColors = [
                        'allow' => ['bg' => '#ecfdf5', 'dot' => '#059669'],
                        'step_up' => ['bg' => '#fffbeb', 'dot' => '#d97706'],
                        'decline' => ['bg' => '#fef2f2', 'dot' => '#dc2626'],
                        ];
                        $dc = $dotColors[$tx->decision->value] ?? ['bg' => '#f1f5f9', 'dot' => '#94a3b8'];
                        $score = $tx->risk_score;
                        $scoreColor = $score < 0.4 ? '#059669' : ($score < 0.7 ? '#d97706' : '#dc2626' );
                            $scorePct=round($score * 100);
                            @endphp
                            <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                                        style="background-color:{{ $dc['bg'] }}">
                                        <div class="w-2 h-2 rounded-full" style="background-color:{{ $dc['dot'] }}"></div>
                                    </div>
                                    <div>
                                        <p class="text-xs font-mono font-medium text-slate-700">****{{ $tx->card_last4 }}</p>
                                        <p class="text-xs text-slate-400">BIN {{ $tx->card_bin }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-3">
                                <p class="text-sm font-semibold text-slate-800">
                                    {{ $tx->currency }} {{ number_format($tx->amount / 100, 2) }}
                                </p>
                            </td>
                            <td class="px-3 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-16 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full"
                                            style="width:{{ $scorePct }}%;background-color:{{ $scoreColor }}">
                                        </div>
                                    </div>
                                    <span class="text-xs font-medium" style="color:{{ $scoreColor }}">
                                        {{ number_format($score, 2) }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-3 py-3">
                                <span class="{{ $tx->decision->badgeClass() }}">{{ $tx->decision->label() }}</span>
                            </td>
                            <td class="px-3 py-3">
                                @if($tx->evidenceBundle)
                                <div class="flex items-center gap-1" style="color:#059669;">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-xs font-medium">Locked</span>
                                </div>
                                @else
                                <span class="text-xs text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-3 py-3">
                                <span class="text-xs text-slate-400">{{ $tx->created_at->diffForHumans() }}</span>
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

            {{-- Live feed --}}
            <div class="card overflow-hidden" x-data="liveFeed()" x-init="init()">
                <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-semibold text-slate-800">Live Feed</h3>
                        <div class="flex items-center gap-1.5">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75"
                                    :style="connected ? 'background:#34d399' : 'background:#fbbf24'"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2"
                                    :style="connected ? 'background:#10b981' : 'background:#f59e0b'"></span>
                            </span>
                            <span class="text-xs"
                                :style="connected ? 'color:#059669' : 'color:#d97706'"
                                x-text="connected ? 'Live' : 'Connecting...'">
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-slate-400" x-text="events.length + ' events'"></span>
                        <button @click="events = []" class="text-xs text-slate-400 hover:text-slate-600">Clear</button>
                    </div>
                </div>

                <div class="overflow-y-auto" style="height:320px;"
                    x-ref="feedContainer"
                    @mouseenter="paused = true"
                    @mouseleave="paused = false">

                    <div x-show="events.length === 0"
                        class="flex flex-col items-center justify-center h-full text-center px-4">
                        <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center mb-3">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <p class="text-xs font-medium text-slate-500">Waiting for transactions...</p>
                        <p class="text-xs text-slate-400 mt-1">Events appear here in real time</p>
                    </div>

                    <div class="divide-y divide-slate-50">
                        <template x-for="event in events" :key="event.id">
                            <div class="px-4 py-2.5 hover:bg-slate-50 transition-colors">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <div class="w-6 h-6 rounded-md flex items-center justify-center shrink-0"
                                            :style="'background-color:' + decisionBg(event.decision)">
                                            <div class="w-1.5 h-1.5 rounded-full"
                                                :style="'background-color:' + decisionColor(event.decision)">
                                            </div>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-mono font-medium text-slate-700 truncate">
                                                ****<span x-text="event.card_last4"></span>
                                                <span class="text-slate-400 ml-1" x-text="'BIN ' + event.card_bin"></span>
                                            </p>
                                            <p class="text-xs text-slate-400">
                                                <span x-text="event.formatted_amount"></span>
                                                <span class="mx-1">·</span>
                                                <span x-text="(event.ip_country || '?') + ' → ' + (event.card_country || '?')"></span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="text-xs font-semibold px-1.5 py-0.5 rounded-md"
                                            :style="'color:' + decisionColor(event.decision) + ';background-color:' + decisionBg(event.decision)"
                                            x-text="event.decision_label">
                                        </span>
                                        <p class="text-xs font-mono mt-0.5"
                                            :style="'color:' + riskColor(event.risk_score)"
                                            x-text="event.risk_score ? event.risk_score.toFixed(3) : '0.000'">
                                        </p>
                                    </div>
                                </div>
                                <p class="text-xs mt-1" style="color:#cbd5e1;" x-text="formatTime(event.scored_at)"></p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Webhook health --}}
            <div class="card p-5">
                <h3 class="text-sm font-semibold text-slate-800 mb-4">Webhook Health</h3>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-slate-500">Delivery rate</span>
                    <span class="text-sm font-bold"
                        style="color:{{ $webhookHealthPct >= 90 ? '#059669' : ($webhookHealthPct >= 70 ? '#d97706' : '#dc2626') }}">
                        {{ $webhookHealthPct }}%
                    </span>
                </div>
                <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden mb-3">
                    <div class="h-full rounded-full"
                        style="width:{{ $webhookHealthPct }}%;background-color:{{ $webhookHealthPct >= 90 ? '#10b981' : ($webhookHealthPct >= 70 ? '#f59e0b' : '#ef4444') }}">
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
                        <div class="flex items-center gap-2 bg-slate-50 rounded-lg px-3 py-2 cursor-pointer"
                            x-data="{ copied: false }"
                            @click="navigator.clipboard.writeText('{{ auth('merchant')->user()->api_key }}'); copied = true; setTimeout(() => copied = false, 2000)">
                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                            <span class="text-xs font-mono text-slate-600 truncate flex-1"
                                x-text="show ? '{{ auth('merchant')->user()->webhook_secret }}' : '••••••••••••••••••••••'">
                            </span>
                            <button @click="show = !show" class="text-slate-400 hover:text-slate-600 shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
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
                <div class="space-y-1">
                    <a href="{{ route('simulate') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-50 transition-colors group">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0" style="background:#eef2ff;">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="#4f46e5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="text-sm text-slate-600 group-hover:text-slate-800">Run simulation</span>
                    </a>
                    <a href="{{ route('transactions') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-50 transition-colors group">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0" style="background:#eff6ff;">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="#3b82f6" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <span class="text-sm text-slate-600 group-hover:text-slate-800">View transactions</span>
                    </a>
                    <a href="{{ route('disputes') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-50 transition-colors group">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0" style="background:#fef2f2;">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="#ef4444" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <span class="text-sm text-slate-600 group-hover:text-slate-800">Manage disputes</span>
                    </a>
                </div>
            </div>

        </div>
    </div>

</div>

{{-- Chart data passed cleanly via hidden elements --}}
<div id="chartData"
    data-labels='@json($volumeLabels)'
    data-counts='@json($volumeCounts)'
    data-risk-low="{{ intval($riskChartData['low']) }}"
    data-risk-medium="{{ intval($riskChartData['medium']) }}"
    data-risk-high="{{ intval($riskChartData['high']) }}"
    data-has-data="{{ $totalTransactions > 0 ? '1' : '0' }}"
    style="display:none;">
</div>

@endsection

@push('scripts')
<script>
    (function() {
        var el = document.getElementById('chartData');
        if (!el) return;

        var hasData = el.getAttribute('data-has-data') === '1';
        var labels = JSON.parse(el.getAttribute('data-labels'));
        var counts = JSON.parse(el.getAttribute('data-counts'));
        var riskLow = parseInt(el.getAttribute('data-risk-low'));
        var riskMed = parseInt(el.getAttribute('data-risk-medium'));
        var riskHigh = parseInt(el.getAttribute('data-risk-high'));

        function initCharts() {
            if (typeof Chart === 'undefined') {
                setTimeout(initCharts, 100);
                return;
            }

            var volumeCtx = document.getElementById('volumeChart');
            if (volumeCtx) {
                new Chart(volumeCtx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Transactions',
                            data: counts,
                            backgroundColor: 'rgba(99,102,241,0.15)',
                            borderColor: 'rgba(99,102,241,0.8)',
                            borderWidth: 2,
                            borderRadius: 6,
                            borderSkipped: false,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                display: false
                            },
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
                                    color: '#94a3b8'
                                },
                                grid: {
                                    color: '#f1f5f9'
                                }
                            },
                            x: {
                                ticks: {
                                    color: '#94a3b8'
                                },
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            var riskCtx = document.getElementById('riskChart');
            if (riskCtx && hasData) {
                new Chart(riskCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Low Risk', 'Medium Risk', 'High Risk'],
                        datasets: [{
                            data: [riskLow, riskMed, riskHigh],
                            backgroundColor: ['#059669', '#d97706', '#dc2626'],
                            borderWidth: 0,
                            hoverOffset: 4,
                        }]
                    },
                    options: {
                        responsive: true,
                        cutout: '72%',
                        plugins: {
                            legend: {
                                display: false
                            },
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
        }

        initCharts();
    })();
</script>

<script>
    function liveFeed() {
        return {
            connected: false,
            paused: false,
            events: [],
            ws: null,
            reconnectTimer: null,

            init() {
                this.connect();
            },

            connect() {
                var self = this;
                try {
                    self.ws = new WebSocket('ws://localhost:3001/ws');

                    self.ws.onopen = function() {
                        self.connected = true;
                        self.ws.send(JSON.stringify({
                            type: 'identify',
                            merchant_id: '{{ auth("merchant")->user()->id }}'
                        }));
                        if (self.reconnectTimer) {
                            clearTimeout(self.reconnectTimer);
                            self.reconnectTimer = null;
                        }
                    };

                    self.ws.onmessage = function(e) {
                        try {
                            var data = JSON.parse(e.data);
                            if (data.type === 'transaction') {
                                self.events.unshift(Object.assign({}, data, {
                                    id: Date.now() + Math.random()
                                }));
                                if (self.events.length > 50) self.events = self.events.slice(0, 50);
                                if (!self.paused && self.$refs.feedContainer) {
                                    self.$nextTick(function() {
                                        self.$refs.feedContainer.scrollTop = 0;
                                    });
                                }
                            }
                        } catch (err) {}
                    };

                    self.ws.onclose = function() {
                        self.connected = false;
                        self.reconnectTimer = setTimeout(function() {
                            self.reconnectTimer = null;
                            self.connect();
                        }, 3000);
                    };

                    self.ws.onerror = function() {
                        self.connected = false;
                    };

                } catch (e) {
                    self.connected = false;
                    self.reconnectTimer = setTimeout(function() {
                        self.reconnectTimer = null;
                        self.connect();
                    }, 3000);
                }
            },

            decisionColor: function(d) {
                return {
                    allow: '#059669',
                    step_up: '#d97706',
                    decline: '#dc2626'
                } [d] || '#64748b';
            },
            decisionBg: function(d) {
                return {
                    allow: '#ecfdf5',
                    step_up: '#fffbeb',
                    decline: '#fef2f2'
                } [d] || '#f1f5f9';
            },
            riskColor: function(s) {
                return s < 0.4 ? '#059669' : s < 0.7 ? '#d97706' : '#dc2626';
            },
            formatTime: function(iso) {
                if (!iso) return '';
                return new Date(iso).toLocaleTimeString('en-NG', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
            }
        };
    }
</script>
@endpush
