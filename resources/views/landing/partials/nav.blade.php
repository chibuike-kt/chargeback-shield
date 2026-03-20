<nav class="fixed top-0 left-0 right-0 z-50 border-b border-slate-200/80"
  style="background: rgba(250,250,250,0.85); backdrop-filter: blur(12px);"
  x-data="{ open: false }">
  <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">

    {{-- Logo --}}
    <a href="/" class="flex items-center gap-2.5">
      <div class="w-8 h-8 rounded-lg flex items-center justify-center"
        style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
        </svg>
      </div>
      <span class="text-base font-bold text-slate-900">Chargeback Shield</span>
    </a>

    {{-- Desktop nav --}}
    <div class="hidden md:flex items-center gap-8">
      <a href="#how-it-works" class="text-sm text-slate-600 hover:text-slate-900 transition-colors">How it works</a>
      <a href="#features" class="text-sm text-slate-600 hover:text-slate-900 transition-colors">Features</a>
      <a href="#pricing" class="text-sm text-slate-600 hover:text-slate-900 transition-colors">Pricing</a>
      <a href="/docs" class="text-sm text-slate-600 hover:text-slate-900 transition-colors">Docs</a>
    </div>

    {{-- CTAs --}}
    <div class="hidden md:flex items-center gap-3">
      <a href="/app/login"
        class="text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">
        Sign in
      </a>
      <a href="/app/register"
        class="text-sm font-semibold px-4 py-2 rounded-lg text-white transition-all hover:opacity-90"
        style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
        Start for Free
      </a>
    </div>

    {{-- Mobile menu button --}}
    <button @click="open = !open" class="md:hidden text-slate-600">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
      </svg>
    </button>
  </div>

  {{-- Mobile menu --}}
  <div x-show="open" x-cloak
    class="md:hidden border-t border-slate-200 bg-white px-6 py-4 space-y-3">
    <a href="#how-it-works" class="block text-sm text-slate-600 py-1">How it works</a>
    <a href="#features" class="block text-sm text-slate-600 py-1">Features</a>
    <a href="#pricing" class="block text-sm text-slate-600 py-1">Pricing</a>
    <a href="/docs" class="block text-sm text-slate-600 py-1">Docs</a>
    <a href="/app/login" class="block text-sm text-slate-600 py-1">Sign in</a>
    <a href="/app/register"
      class="block text-sm font-semibold px-4 py-2.5 rounded-lg text-white text-center"
      style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
      Start for Free
    </a>
  </div>
</nav>
