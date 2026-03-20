<section class="py-24 px-6">
  <div class="max-w-6xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

      {{-- Left --}}
      <div>
        <span class="text-xs font-bold uppercase tracking-widest text-indigo-500 mb-4 block">
          Risk engine
        </span>
        <h2 class="text-4xl font-black text-slate-900 mb-5">
          Six signals.<br>One composite score.
        </h2>
        <p class="text-lg text-slate-500 leading-relaxed mb-8">
          Every transaction is scored across 6 weighted signals before a decision is made.
          The score is fully explainable — you can see exactly why a transaction
          scored 0.83 and what pushed it over the threshold.
        </p>
        <a href="/docs#scoring"
          class="inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 hover:text-indigo-500">
          Read the scoring docs
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" />
          </svg>
        </a>
      </div>

      {{-- Right — signal bars --}}
      <div class="bg-white rounded-2xl p-7 border border-slate-200 card-glow">
        @php
        $signals = [
        ['name' => 'Velocity', 'weight' => 25, 'score' => 0.12, 'raw' => 'tx_hour:2 spend_24h:45,000'],
        ['name' => 'Geo Mismatch', 'weight' => 20, 'score' => 0.05, 'raw' => 'NG == NG (match)'],
        ['name' => 'BIN Risk', 'weight' => 20, 'score' => 0.05, 'raw' => '459234 (known low-risk)'],
        ['name' => 'Device Fingerprint', 'weight' => 15, 'score' => 0.10, 'raw' => 'fp_abc123 (valid)'],
        ['name' => 'Session Age', 'weight' => 10, 'score' => 0.05, 'raw' => '900s (established)'],
        ['name' => 'Amount Risk', 'weight' => 10, 'score' => 0.10, 'raw' => 'NGN 5,000 (normal)'],
        ];
        @endphp

        <div class="space-y-4 mb-5">
          @foreach($signals as $signal)
          <div>
            <div class="flex items-center justify-between mb-1.5">
              <div class="flex items-center gap-2">
                <span class="text-xs font-semibold text-slate-700">{{ $signal['name'] }}</span>
                <span class="text-xs text-slate-400">{{ $signal['weight'] }}%</span>
              </div>
              <span class="text-xs font-mono font-bold text-emerald-600">
                {{ number_format($signal['score'], 3) }}
              </span>
            </div>
            <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
              <div class="h-full rounded-full bg-emerald-400"
                style="width: {{ $signal['score'] * 100 }}%;"></div>
            </div>
            <p class="text-xs text-slate-400 mt-1">{{ $signal['raw'] }}</p>
          </div>
          @endforeach
        </div>

        <div class="border-t border-slate-100 pt-4 flex items-center justify-between">
          <span class="text-sm font-semibold text-slate-700">Composite Score</span>
          <div class="flex items-center gap-2">
            <span class="text-xl font-black font-mono text-emerald-600">0.124</span>
            <span class="badge badge-green text-xs">Approved</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
