<section id="features" class="py-24 px-6"
  style="background: linear-gradient(180deg, #fafafa 0%, #f1f5f9 100%);">
  <div class="max-w-6xl mx-auto">

    <div class="text-center mb-16">
      <span class="text-xs font-bold uppercase tracking-widest text-indigo-500 mb-4 block">
        Features
      </span>
      <h2 class="text-4xl font-black text-slate-900 mb-4">
        Everything you need to win disputes.
      </h2>
      <p class="text-lg text-slate-500 max-w-xl mx-auto">
        Built for the African payments landscape. Not a port of a Western product.
      </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
      @php
      $features = [
      [
      'title' => 'Real-time risk scoring',
      'desc' => '6 weighted signals computed in under 40ms. Velocity windows, geo mismatch, device fingerprint, BIN risk — all of it.',
      'color' => '#6366f1',
      'bg' => '#eef2ff',
      'icon' => '
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13 10V3L4 14h7v7l9-11h-7z" />',
      ],
      [
      'title' => 'Cryptographic evidence vault',
      'desc' => 'AES-256 encrypted, HMAC-SHA256 signed evidence bundle locked at every approval. Tamper-proof. Immutable. Forever.',
      'color' => '#059669',
      'bg' => '#ecfdf5',
      'icon' => '
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />',
      ],
      [
      'title' => 'Instant dispute responses',
      'desc' => '15 Visa and Mastercard reason codes supported. Tailored response strategy and winning argument for each one. Seconds not days.',
      'color' => '#8b5cf6',
      'bg' => '#f5f3ff',
      'icon' => '
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />',
      ],
      [
      'title' => 'Signed webhook delivery',
      'desc' => 'Every event fires a signed webhook. HMAC-SHA256 verified. Exponential backoff retry. Full delivery log with manual re-trigger.',
      'color' => '#d97706',
      'bg' => '#fffbeb',
      'icon' => '
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />',
      ],
      [
      'title' => 'Live transaction feed',
      'desc' => 'WebSocket-powered real-time feed. Every transaction scored, every event visible. Feels like a fraud monitoring terminal.',
      'color' => '#06b6d4',
      'bg' => '#ecfeff',
      'icon' => '
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />',
      ],
      [
      'title' => 'Idempotent by design',
      'desc' => 'Same request sent twice returns the cached response. Redis-backed, 24-hour TTL. Financial middleware that actually works correctly.',
      'color' => '#dc2626',
      'bg' => '#fef2f2',
      'icon' => '
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />',
      ],
      ];
      @endphp

      @foreach($features as $feature)
      <div class="feature-card bg-white rounded-2xl p-6 border border-slate-200">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-4"
          style="background:{{ $feature['bg'] }};">
          <svg class="w-5 h-5" fill="none" stroke="{{ $feature['color'] }}" viewBox="0 0 24 24">
            {!! $feature['icon'] !!}
          </svg>
        </div>
        <h3 class="text-base font-bold text-slate-900 mb-2">{{ $feature['title'] }}</h3>
        <p class="text-sm text-slate-500 leading-relaxed">{{ $feature['desc'] }}</p>
      </div>
      @endforeach
    </div>
  </div>
</section>
