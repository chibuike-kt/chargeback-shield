<section class="hero-glow pt-32 pb-20 px-6 relative overflow-hidden">

  {{-- Background decoration --}}
  <div class="absolute inset-0 noise pointer-events-none"></div>
  <div class="absolute top-20 left-1/4 w-96 h-96 rounded-full blur-3xl pointer-events-none"
    style="background: rgba(99,102,241,0.06);"></div>
  <div class="absolute top-40 right-1/4 w-64 h-64 rounded-full blur-3xl pointer-events-none"
    style="background: rgba(139,92,246,0.06);"></div>

  <div class="max-w-6xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

      {{-- Left — copy --}}
      <div>
        {{-- Badge --}}
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold mb-6 border"
          style="background: rgba(99,102,241,0.06); border-color: rgba(99,102,241,0.2); color: #6366f1;">
          <span class="relative flex h-1.5 w-1.5">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-indigo-500"></span>
          </span>
          Built for African fintechs
        </div>

        {{-- Headline --}}
        <h1 class="text-5xl lg:text-6xl font-black leading-tight tracking-tight text-slate-900 mb-6">
          Stop losing
          <span class="gradient-text">chargebacks</span>
          you should win.
        </h1>

        {{-- Subheadline --}}
        <p class="text-lg text-slate-500 leading-relaxed mb-8 max-w-lg">
          Chargeback Shield intercepts every card transaction, locks
          cryptographic evidence at approval, and generates dispute
          responses in seconds — not days.
        </p>

        {{-- CTAs --}}
        <div class="flex flex-col sm:flex-row gap-3 mb-10">
          <a href="/app/register"
            class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl text-white font-semibold text-sm transition-all hover:opacity-90 hover:shadow-lg"
            style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
            Start for Free
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" />
            </svg>
          </a>
          <a href="/docs"
            class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl text-slate-700 font-semibold text-sm border border-slate-200 hover:border-slate-300 hover:bg-white transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
            </svg>
            View API docs
          </a>
        </div>

        {{-- Social proof --}}
        <div class="flex items-center gap-4 text-xs text-slate-400">
          <div class="flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            No credit card required
          </div>
          <div class="flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            Live in 15 minutes
          </div>
          <div class="flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            One API endpoint
          </div>
        </div>
      </div>

      {{-- Right — live feed mockup --}}
      <div class="relative" x-data="heroFeed()">
        <div class="relative rounded-2xl overflow-hidden border border-slate-200 card-glow bg-white">

          {{-- Terminal header --}}
          <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100"
            style="background: #f8fafc;">
            <div class="flex items-center gap-2">
              <div class="w-2.5 h-2.5 rounded-full bg-red-400"></div>
              <div class="w-2.5 h-2.5 rounded-full bg-amber-400"></div>
              <div class="w-2.5 h-2.5 rounded-full bg-emerald-400"></div>
            </div>
            <div class="flex items-center gap-1.5">
              <span class="relative flex h-1.5 w-1.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
              </span>
              <span class="text-xs text-slate-500 font-medium">Live transaction feed</span>
            </div>
            <span class="text-xs font-mono text-slate-400" x-text="events.length + ' events'"></span>
          </div>

          {{-- Feed --}}
          <div class="divide-y divide-slate-50" style="min-height: 340px;">
            <template x-for="event in events" :key="event.id">
              <div class="feed-item px-4 py-3 hover:bg-slate-50 transition-colors">
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-3">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0"
                      :style="'background:' + decisionBg(event.decision)">
                      <div class="w-1.5 h-1.5 rounded-full"
                        :style="'background:' + decisionColor(event.decision)">
                      </div>
                    </div>
                    <div>
                      <p class="text-xs font-mono font-semibold text-slate-700">
                        ****<span x-text="event.last4"></span>
                        <span class="text-slate-400 font-normal ml-1" x-text="'· ' + event.bin"></span>
                      </p>
                      <p class="text-xs text-slate-400 mt-0.5">
                        <span x-text="event.amount"></span>
                        <span class="mx-1">·</span>
                        <span x-text="event.route"></span>
                      </p>
                    </div>
                  </div>
                  <div class="text-right">
                    <span class="text-xs font-bold px-2 py-0.5 rounded-md"
                      :style="'color:' + decisionColor(event.decision) + ';background:' + decisionBg(event.decision)"
                      x-text="event.label">
                    </span>
                    <p class="text-xs font-mono mt-0.5 font-semibold"
                      :style="'color:' + scoreColor(event.score)"
                      x-text="event.score.toFixed(3)">
                    </p>
                  </div>
                </div>
              </div>
            </template>

            {{-- Empty state --}}
            <div x-show="events.length === 0"
              class="flex items-center justify-center h-40">
              <p class="text-xs text-slate-400">Initialising feed...</p>
            </div>
          </div>

          {{-- Code snippet at bottom --}}
          <div class="border-t border-slate-100 px-4 py-3"
            style="background: #0f172a;">
            <p class="text-xs font-mono" style="color: #94a3b8;">
              <span style="color: #6366f1;">POST</span>
              <span style="color: #e2e8f0;"> /api/v1/transaction/intercept</span>
            </p>
            <p class="text-xs font-mono mt-1" style="color: #94a3b8;">
              <span style="color: #34d399;">→</span>
              <span style="color: #e2e8f0;"> decision: </span>
              <span style="color: #fbbf24;">"allow"</span>
              <span style="color: #e2e8f0;"> · score: </span>
              <span style="color: #34d399;">0.124</span>
              <span style="color: #e2e8f0;"> · evidence: </span>
              <span style="color: #34d399;">locked</span>
            </p>
          </div>
        </div>

        {{-- Floating evidence badge --}}
        <div class="absolute -left-6 top-1/3 bg-white rounded-xl px-3 py-2.5 border border-slate-200 shadow-lg"
          style="animation: float 3s ease-in-out infinite;">
          <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded-lg flex items-center justify-center"
              style="background: #ecfdf5;">
              <svg class="w-3.5 h-3.5" style="color:#059669;" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
              </svg>
            </div>
            <div>
              <p class="text-xs font-bold text-slate-700">Evidence locked</p>
              <p class="text-xs text-slate-400">HMAC-SHA256 signed</p>
            </div>
          </div>
        </div>

        {{-- Floating dispute badge --}}
        <div class="absolute -right-6 bottom-1/3 bg-white rounded-xl px-3 py-2.5 border border-slate-200 shadow-lg"
          style="animation: float 3s ease-in-out infinite; animation-delay: 1.5s;">
          <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded-lg flex items-center justify-center"
              style="background: #eef2ff;">
              <svg class="w-3.5 h-3.5" style="color:#6366f1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
            <div>
              <p class="text-xs font-bold text-slate-700">Response ready</p>
              <p class="text-xs text-slate-400">in 0.8 seconds</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
