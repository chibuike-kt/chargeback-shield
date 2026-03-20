<footer class="border-t border-slate-100 py-12 px-6">
  <div class="max-w-6xl mx-auto">
    <div class="flex flex-col md:flex-row items-start justify-between gap-8">

      {{-- Brand --}}
      <div class="max-w-xs">
        <div class="flex items-center gap-2.5 mb-3">
          <div class="w-7 h-7 rounded-lg flex items-center justify-center"
            style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
          </div>
          <span class="text-sm font-bold text-slate-900">Chargeback Shield</span>
        </div>
        <p class="text-sm text-slate-400 leading-relaxed">
          Real-time chargeback protection for African fintechs.
          Built by Atlas Tech.
        </p>
      </div>

      {{-- Links --}}
      <div class="grid grid-cols-2 gap-8">
        <div>
          <p class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3">Product</p>
          <div class="space-y-2">
            <a href="#how-it-works" class="block text-sm text-slate-500 hover:text-slate-700">How it works</a>
            <a href="#features" class="block text-sm text-slate-500 hover:text-slate-700">Features</a>
            <a href="#pricing" class="block text-sm text-slate-500 hover:text-slate-700">Pricing</a>
            <a href="/app/register" class="block text-sm text-slate-500 hover:text-slate-700">Sign up</a>
          </div>
        </div>
        <div>
          <p class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3">Developers</p>
          <div class="space-y-2">
            <a href="/docs" class="block text-sm text-slate-500 hover:text-slate-700">Documentation</a>
            <a href="/docs/quickstart" class="block text-sm text-slate-500 hover:text-slate-700">Quickstart</a>
            <a href="/docs/api" class="block text-sm text-slate-500 hover:text-slate-700">API Reference</a>
            <a href="https://github.com/chibuike-kt/chargeback-shield"
              class="block text-sm text-slate-500 hover:text-slate-700">GitHub</a>
          </div>
        </div>
      </div>
    </div>

    <div class="border-t border-slate-100 mt-10 pt-6 flex flex-col md:flex-row items-center justify-between gap-3">
      <p class="text-xs text-slate-400">
        © {{ date('Y') }} Atlas Tech. All rights reserved.
      </p>
      <p class="text-xs text-slate-400">
        Built for the #RaenestXDevCareer Hackathon
      </p>
    </div>
  </div>
</footer>
