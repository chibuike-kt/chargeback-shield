<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Dashboard') — Chargeback Shield</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full bg-surface-50" x-data="{ sidebarOpen: true }">

  <div class="flex h-full">

    {{-- Sidebar --}}
    @include('components.sidebar')

    {{-- Main content --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

      {{-- Top bar --}}
      @include('components.topbar')

      {{-- Page content --}}
      <main class="flex-1 overflow-y-auto p-6">

        @if(session('success'))
        <div class="mb-5 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm flex items-center gap-2">
          <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
          </svg>
          {{ session('success') }}
        </div>
        @endif

        @yield('content')
      </main>
    </div>
  </div>

  @stack('scripts')
</body>

</html>
