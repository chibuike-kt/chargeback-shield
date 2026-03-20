<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Chargeback Shield — Real-time chargeback protection for African fintechs</title>
  <meta name="description" content="Chargeback Shield intercepts every card transaction, locks cryptographic evidence at approval, and auto-generates dispute responses in seconds. Built for African fintechs.">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <style>
    * {
      font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .gradient-text {
      background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #06b6d4 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .hero-glow {
      background: radial-gradient(ellipse 80% 50% at 50% -20%, rgba(99, 102, 241, 0.15), transparent);
    }

    .card-glow {
      box-shadow: 0 0 0 1px rgba(99, 102, 241, 0.1), 0 4px 24px rgba(99, 102, 241, 0.08);
    }

    .feature-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 0 0 1px rgba(99, 102, 241, 0.2), 0 8px 32px rgba(99, 102, 241, 0.12);
    }

    .feature-card {
      transition: all 0.2s ease;
    }

    @keyframes float {

      0%,
      100% {
        transform: translateY(0px);
      }

      50% {
        transform: translateY(-8px);
      }
    }

    @keyframes slideIn {
      from {
        opacity: 0;
        transform: translateX(20px);
      }

      to {
        opacity: 1;
        transform: translateX(0);
      }
    }

    .feed-item {
      animation: slideIn 0.4s ease forwards;
    }

    .noise {
      background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.03'/%3E%3C/svg%3E");
    }
  </style>
</head>

<body class="bg-[#fafafa] text-slate-900 antialiased">

  @yield('content')

  @stack('scripts')
</body>

</html>
