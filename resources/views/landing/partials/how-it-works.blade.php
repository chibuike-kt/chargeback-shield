<section id="how-it-works" class="py-24 px-6 border-t border-slate-100">
  <div class="max-w-6xl mx-auto">

    <div class="text-center mb-16">
      <span class="text-xs font-bold uppercase tracking-widest text-indigo-500 mb-4 block">
        How it works
      </span>
      <h2 class="text-4xl font-black text-slate-900 mb-4">
        Three steps. No surprises.
      </h2>
      <p class="text-lg text-slate-500 max-w-xl mx-auto">
        One API endpoint. Everything else is automatic.
      </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 relative">

      {{-- Connector lines --}}
      <div class="hidden lg:block absolute top-10 left-1/3 right-1/3 h-px"
        style="background: linear-gradient(90deg, transparent, rgba(99,102,241,0.3), transparent);">
      </div>

      @php
      $steps = [
      [
      'number' => '01',
      'title' => 'Intercept',
      'desc' => 'Your payment system calls POST /api/v1/transaction/intercept for every card transaction. Takes under 100ms. Non-blocking.',
      'color' => '#6366f1',
      'bg' => '#eef2ff',
      'code' => 'POST /api/v1/transaction/intercept',
      ],
      [
      'number' => '02',
      'title' => 'Score + Lock',
      'desc' => '6 real-time signals fire. Risk score computed. Evidence bundle locked with AES-256 encryption and HMAC-SHA256 signature.',
      'color' => '#8b5cf6',
      'bg' => '#f5f3ff',
      'code' => '→ score: 0.124 · decision: allow',
      ],
      [
      'number' => '03',
      'title' => 'Dispute? Done.',
      'desc' => 'Chargeback lands. One API call. The evidence bundle is retrieved, signature verified, and a full response document generated.',
      'color' => '#06b6d4',
      'bg' => '#ecfeff',
      'code' => 'POST /api/v1/dispute → response ready',
      ],
      ];
      @endphp

      @foreach($steps as $step)
      <div class="relative">
        <div class="bg-white rounded-2xl p-7 border border-slate-200 card-glow h-full">
          <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-sm"
              style="background:{{ $step['bg'] }};color:{{ $step['color'] }};">
              {{ $step['number'] }}
            </div>
            <h3 class="text-lg font-black text-slate-900">{{ $step['title'] }}</h3>
          </div>
          <p class="text-sm text-slate-500 leading-relaxed mb-5">
            {{ $step['desc'] }}
          </p>
          <div class="rounded-lg px-3 py-2" style="background:#0f172a;">
            <p class="text-xs font-mono" style="color:#94a3b8;">{{ $step['code'] }}</p>
          </div>
        </div>
      </div>
      @endforeach

    </div>
  </div>
</section>
