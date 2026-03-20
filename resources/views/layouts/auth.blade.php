<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Chargeback Shield') — Atlas Tech</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full bg-surface-50">

  <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">

    {{-- Logo --}}
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div class="flex items-center justify-center gap-2 mb-8">
        <div class="w-9 h-9 bg-brand-600 rounded-xl flex items-center justify-center">
          <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
          </svg>
        </div>
        <div>
          <p class="text-xs text-slate-500 leading-none">Atlas Tech</p>
          <h1 class="text-base font-bold text-slate-800 leading-tight">Chargeback Shield</h1>
        </div>
      </div>
    </div>

    {{-- Card --}}
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div class="card px-8 py-8">
        @yield('content')
      </div>
    </div>

    <p class="mt-6 text-center text-xs text-slate-400">
      © {{ date('Y') }} Atlas Tech. Real-time chargeback protection for African fintechs.
    </p>
  </div>

</body>

</html>
