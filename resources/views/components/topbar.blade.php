<header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6 shrink-0">

  <div class="flex items-center gap-4">
    {{-- Sidebar toggle --}}
    <button @click="sidebarOpen = !sidebarOpen"
      class="text-slate-400 hover:text-slate-600 transition-colors">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
      </svg>
    </button>

    {{-- Page title slot --}}
    <h1 class="text-base font-semibold text-slate-800">@yield('page-title', 'Dashboard')</h1>
  </div>

  <div class="flex items-center gap-3">

    {{-- Live indicator --}}
    <div class="flex items-center gap-2 px-3 py-1.5 bg-emerald-50 rounded-full border border-emerald-200">
      <span class="relative flex h-2 w-2">
        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
      </span>
      <span class="text-xs font-medium text-emerald-700">Live</span>
    </div>

    {{-- API Key quick copy --}}
    <div class="hidden md:flex items-center gap-2 px-3 py-1.5 bg-surface-100 rounded-lg border border-slate-200"
      x-data="{ copied: false }"
      @click="
                navigator.clipboard.writeText('{{ auth('merchant')->user()->api_key ?? '' }}');
                copied = true;
                setTimeout(() => copied = false, 2000)
            "
      title="Click to copy API key"
      style="cursor:pointer">
      <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
      </svg>
      <span class="text-xs font-mono text-slate-500" x-text="copied ? 'Copied!' : '{{ substr(auth('merchant')->user()->api_key ?? 'cs_live_...', 0, 18) }}...'"></span>
    </div>

    {{-- Logout --}}
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit"
        class="flex items-center gap-2 px-3 py-1.5 text-sm text-slate-600 hover:text-slate-800 hover:bg-slate-50 rounded-lg transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
        </svg>
        Sign out
      </button>
    </form>
  </div>
</header>
