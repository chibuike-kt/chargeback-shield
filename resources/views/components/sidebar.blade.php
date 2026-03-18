<aside class="w-64 shrink-0 bg-white border-r border-slate-200 flex flex-col h-full"
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
    [
    'route' => 'dashboard',
    'label' => 'Dashboard',
    'svg' => '
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />',
    ],
    [
    'route' => 'transactions',
    'label' => 'Transactions',
    'svg' => '
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13 10V3L4 14h7v7l9-11h-7z" />',
    ],
    [
    'route' => 'disputes',
    'label' => 'Disputes',
    'svg' => '
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />',
    ],
    [
    'route' => 'webhooks',
    'label' => 'Webhooks',
    'svg' => '
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />',
    ],
    [
    'route' => 'trust-registry',
    'label' => 'Trust Registry',
    'svg' => '
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />',
    ],
    [
    'route' => 'simulate',
    'label' => 'Simulate',
    'svg' => '
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
    ],
    [
    'route' => 'audit-log',
    'label' => 'Audit Log',
    'svg' => '
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />',
    ],
    [
    'route' => 'settings',
    'label' => 'Settings',
    'svg' => '
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />',
    ],
    ];
    @endphp

    @foreach($navItems as $item)
    @php
    $isActive = request()->routeIs($item['route']) || request()->routeIs($item['route'].'.*');
    @endphp
    <a href="{{ route_if_exists($item['route']) }}"
      class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors duration-150
                    {{ $isActive
                        ? 'bg-brand-50 text-brand-700 font-medium'
                        : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800' }}">

      <svg class="w-5 h-5 shrink-0 {{ $isActive ? 'text-brand-600' : 'text-slate-400' }}"
        fill="none" stroke="currentColor" viewBox="0 0 24 24">
        {!! $item['svg'] !!}
      </svg>

      <span x-show="sidebarOpen" class="truncate">{{ $item['label'] }}</span>

      @if($isActive)
      <span x-show="sidebarOpen" class="ml-auto w-1.5 h-1.5 rounded-full bg-brand-500"></span>
      @endif
    </a>
    @endforeach
  </nav>

  {{-- Merchant footer --}}
  <div class="border-t border-slate-200 p-3 shrink-0">
    <div class="flex items-center gap-3 px-2 py-2">
      <div class="w-8 h-8 rounded-full bg-brand-100 flex items-center justify-center shrink-0">
        <span class="text-xs font-bold text-brand-700">
          {{ strtoupper(substr(auth('merchant')->user()->company_name, 0, 2)) }}
        </span>
      </div>
      <div x-show="sidebarOpen" class="min-w-0">
        <p class="text-sm font-medium text-slate-800 truncate">
          {{ auth('merchant')->user()->company_name }}
        </p>
        <p class="text-xs text-slate-400 truncate">
          {{ auth('merchant')->user()->email }}
        </p>
      </div>
    </div>
  </div>

</aside>


