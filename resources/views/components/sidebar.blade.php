<aside
  class="w-64 shrink-0 bg-white border-r border-slate-200 flex flex-col h-full"
  :class="sidebarOpen ? 'w-64' : 'w-16'">
  {{-- Logo --}}
  <div class="h-16 flex items-center gap-3 px-4 border-b border-slate-200 shrink-0">
    <div class="w-8 h-8 bg-brand-600 rounded-lg flex items-center justify-center shrink-0">
      <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
      </svg>
    </div>
    <div x-show="sidebarOpen">
      <p class="text-[10px] text-slate-400 leading-none uppercase tracking-wider">Atlas Tech</p>
      <p class="text-sm font-bold text-slate-800 leading-tight">Chargeback Shield</p>
    </div>
  </div>

  {{-- Navigation --}}
  <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">

    @php
    $navItems = [
    ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'grid'],
    ['route' => 'transactions', 'label' => 'Transactions', 'icon' => 'lightning'],
    ['route' => 'disputes', 'label' => 'Disputes', 'icon' => 'shield'],
    ['route' => 'webhooks', 'label' => 'Webhooks', 'icon' => 'bell'],
    ['route' => 'trust-registry','label' => 'Trust Registry','icon' => 'badge'],
    ['route' => 'simulate', 'label' => 'Simulate', 'icon' => 'play'],
    ['route' => 'audit-log', 'label' => 'Audit Log', 'icon' => 'clock'],
    ['route' => 'settings', 'label' => 'Settings', 'icon' => 'cog'],
    ];
    @endphp

    @foreach($navItems as $item)
    @php
    $isActive = request()->routeIs($item['route']) || request()->routeIs($item['route'].'.*');
    @endphp

    href="{{ route_if_exists($item['route']) }}"
    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors duration-150 group
    {{ $isActive
                        ? 'bg-brand-50 text-brand-700 font-medium'
                        : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800' }}"
    >
    @include('components.icons.'.$item['icon'], ['active' => $isActive])
    <span x-show="sidebarOpen" class="truncate">{{ $item['label'] }}</span>
    @if($isActive)
    <span x-show="sidebarOpen" class="ml-auto w-1.5 h-1.5 rounded-full bg-brand-500"></span>
    @endif
    </a>
    @endforeach
  </nav>

  {{-- Merchant info footer --}}
  <div class="border-t border-slate-200 p-3 shrink-0">
    <div class="flex items-center gap-3 px-2 py-2">
      <div class="w-8 h-8 rounded-full bg-brand-100 flex items-center justify-center shrink-0">
        <span class="text-xs font-bold text-brand-700">
          {{ strtoupper(substr(auth('merchant')->user()->company_name, 0, 2)) }}
        </span>
      </div>
      <div x-show="sidebarOpen" class="min-w-0">
        <p class="text-sm font-medium text-slate-800 truncate">{{ auth('merchant')->user()->company_name }}</p>
        <p class="text-xs text-slate-400 truncate">{{ auth('merchant')->user()->email }}</p>
      </div>
    </div>
  </div>
</aside>
