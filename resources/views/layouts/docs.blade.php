<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Docs') — Chargeback Shield</title>
  <meta name="description" content="Chargeback Shield API documentation. Integrate real-time chargeback protection into your African fintech in under 15 minutes.">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <style>
    * {
      font-family: 'Plus Jakarta Sans', sans-serif;
    }

    code,
    pre,
    .mono {
      font-family: 'JetBrains Mono', monospace;
    }

    .docs-content h2 {
      font-size: 1.5rem;
      font-weight: 800;
      color: #0f172a;
      margin-top: 2.5rem;
      margin-bottom: 1rem;
      padding-top: 1rem;
      border-top: 1px solid #f1f5f9;
    }

    .docs-content h2:first-child {
      border-top: none;
      margin-top: 0;
      padding-top: 0;
    }

    .docs-content h3 {
      font-size: 1rem;
      font-weight: 700;
      color: #1e293b;
      margin-top: 1.75rem;
      margin-bottom: 0.75rem;
    }

    .docs-content p {
      font-size: 0.9375rem;
      color: #475569;
      line-height: 1.75;
      margin-bottom: 1rem;
    }

    .docs-content ul {
      margin-bottom: 1rem;
      padding-left: 1.25rem;
    }

    .docs-content ul li {
      font-size: 0.9375rem;
      color: #475569;
      line-height: 1.75;
      margin-bottom: 0.25rem;
      list-style-type: disc;
    }

    .docs-content a {
      color: #6366f1;
      text-decoration: underline;
      text-underline-offset: 2px;
    }

    .code-block {
      background: #0f172a;
      border-radius: 0.75rem;
      padding: 1.25rem 1.5rem;
      margin-bottom: 1.25rem;
      overflow-x: auto;
      border: 1px solid #1e293b;
    }

    .code-block pre {
      font-size: 0.8125rem;
      line-height: 1.7;
      color: #e2e8f0;
      margin: 0;
    }

    .code-block .comment {
      color: #64748b;
    }

    .code-block .keyword {
      color: #818cf8;
    }

    .code-block .string {
      color: #34d399;
    }

    .code-block .key {
      color: #93c5fd;
    }

    .code-block .value {
      color: #fbbf24;
    }

    .code-block .method {
      color: #f472b6;
    }

    .code-block .url {
      color: #a78bfa;
    }

    .inline-code {
      font-family: 'JetBrains Mono', monospace;
      font-size: 0.8125rem;
      background: #f1f5f9;
      color: #4f46e5;
      padding: 0.125rem 0.4rem;
      border-radius: 0.25rem;
      border: 1px solid #e2e8f0;
    }

    .param-table th {
      font-size: 0.75rem;
      font-weight: 600;
      color: #64748b;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      padding: 0.625rem 1rem;
      background: #f8fafc;
      border-bottom: 1px solid #e2e8f0;
      text-align: left;
    }

    .param-table td {
      font-size: 0.875rem;
      padding: 0.75rem 1rem;
      border-bottom: 1px solid #f1f5f9;
      vertical-align: top;
    }

    .param-table tr:last-child td {
      border-bottom: none;
    }

    .method-badge {
      font-family: 'JetBrains Mono', monospace;
      font-size: 0.6875rem;
      font-weight: 700;
      padding: 0.2rem 0.5rem;
      border-radius: 0.25rem;
    }

    .method-post {
      background: #ecfdf5;
      color: #059669;
    }

    .method-get {
      background: #eff6ff;
      color: #3b82f6;
    }

    .nav-link {
      display: block;
      font-size: 0.8125rem;
      color: #64748b;
      padding: 0.3rem 0.75rem;
      border-radius: 0.375rem;
      transition: all 0.15s;
      text-decoration: none;
    }

    .nav-link:hover {
      background: #f1f5f9;
      color: #1e293b;
    }

    .nav-link.active {
      background: #eef2ff;
      color: #4f46e5;
      font-weight: 600;
    }

    .nav-section {
      font-size: 0.6875rem;
      font-weight: 700;
      color: #94a3b8;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      padding: 0.5rem 0.75rem 0.25rem;
      margin-top: 1rem;
    }

    .callout {
      border-radius: 0.75rem;
      padding: 1rem 1.25rem;
      margin-bottom: 1.25rem;
      border-left: 3px solid;
      font-size: 0.875rem;
      line-height: 1.6;
    }

    .callout-info {
      background: #eef2ff;
      border-color: #6366f1;
      color: #3730a3;
    }

    .callout-warning {
      background: #fffbeb;
      border-color: #d97706;
      color: #92400e;
    }

    .callout-success {
      background: #ecfdf5;
      border-color: #059669;
      color: #065f46;
    }

    .callout-danger {
      background: #fef2f2;
      border-color: #dc2626;
      color: #991b1b;
    }

    :target {
      scroll-margin-top: 5rem;
    }
  </style>
</head>

<body class="bg-white text-slate-900 antialiased">

  {{-- Top nav --}}
  <header class="fixed top-0 left-0 right-0 z-50 border-b border-slate-200 bg-white h-14 flex items-center px-6"
    style="backdrop-filter: blur(8px);">
    <div class="flex items-center gap-8 w-full max-w-7xl mx-auto">

      {{-- Logo --}}
      <a href="/" class="flex items-center gap-2 shrink-0">
        <div class="w-6 h-6 rounded-md flex items-center justify-center"
          style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
          <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
          </svg>
        </div>
        <span class="text-sm font-bold text-slate-900">Chargeback Shield</span>
        <span class="text-slate-300 text-sm">/</span>
        <span class="text-sm text-slate-500">Docs</span>
      </a>

      <div class="flex-1"></div>

      <div class="flex items-center gap-4">
        <a href="https://github.com/chibuike-kt/chargeback-shield"
          class="text-sm text-slate-500 hover:text-slate-700 flex items-center gap-1.5">
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z" />
          </svg>
          GitHub
        </a>
        <a href="/app/register"
          class="text-sm font-semibold px-4 py-1.5 rounded-lg text-white"
          style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
          Get API Key
        </a>
      </div>
    </div>
  </header>

  <div class="flex pt-14 max-w-7xl mx-auto">

    {{-- Sidebar --}}
    @include('docs.partials.sidebar')

    {{-- Content --}}
    <main class="flex-1 min-w-0 px-8 py-10 max-w-3xl docs-content">
      @yield('content')
    </main>

    {{-- Right — on-page nav --}}
    <aside class="hidden xl:block w-52 shrink-0 py-10 pl-4">
      <div class="sticky top-20">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">On this page</p>
        @yield('on-page-nav')
      </div>
    </aside>
  </div>

  @stack('scripts')
</body>

</html>
