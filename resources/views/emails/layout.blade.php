<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $subject ?? 'Chargeback Shield' }}</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI',
        Roboto, 'Helvetica Neue', Arial, sans-serif;
      background-color: #f8fafc;
      color: #1e293b;
      -webkit-font-smoothing: antialiased;
    }

    .wrapper {
      max-width: 600px;
      margin: 40px auto;
      padding: 0 16px 40px;
    }

    .header {
      text-align: center;
      padding: 32px 0 24px;
    }

    .logo {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      text-decoration: none;
    }

    .logo-icon {
      width: 36px;
      height: 36px;
      background: linear-gradient(135deg, #6366f1, #8b5cf6);
      border-radius: 10px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    .logo-text {
      font-size: 16px;
      font-weight: 700;
      color: #1e293b;
    }

    .card {
      background: #ffffff;
      border-radius: 16px;
      border: 1px solid #e2e8f0;
      overflow: hidden;
    }

    .card-header {
      padding: 28px 32px 24px;
      border-bottom: 1px solid #f1f5f9;
    }

    .card-body {
      padding: 28px 32px;
    }

    .card-footer {
      padding: 20px 32px;
      background: #f8fafc;
      border-top: 1px solid #f1f5f9;
    }

    h1 {
      font-size: 22px;
      font-weight: 800;
      color: #0f172a;
      line-height: 1.3;
      margin-bottom: 8px;
    }

    h2 {
      font-size: 16px;
      font-weight: 700;
      color: #1e293b;
      margin-bottom: 8px;
      margin-top: 24px;
    }

    h2:first-child {
      margin-top: 0;
    }

    p {
      font-size: 15px;
      color: #475569;
      line-height: 1.7;
      margin-bottom: 16px;
    }

    p:last-child {
      margin-bottom: 0;
    }

    .btn {
      display: inline-block;
      padding: 13px 28px;
      border-radius: 10px;
      font-size: 14px;
      font-weight: 700;
      text-decoration: none;
      text-align: center;
    }

    .btn-primary {
      background: linear-gradient(135deg, #6366f1, #8b5cf6);
      color: #ffffff !important;
    }

    .btn-danger {
      background: #dc2626;
      color: #ffffff !important;
    }

    .btn-secondary {
      background: #f1f5f9;
      color: #374151 !important;
      border: 1px solid #e2e8f0;
    }

    .stat-row {
      display: flex;
      gap: 12px;
      margin-bottom: 16px;
    }

    .stat-box {
      flex: 1;
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 10px;
      padding: 16px;
      text-align: center;
    }

    .stat-value {
      font-size: 24px;
      font-weight: 800;
      color: #0f172a;
      display: block;
    }

    .stat-label {
      font-size: 11px;
      color: #94a3b8;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      margin-top: 4px;
      display: block;
    }

    .detail-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }

    .detail-table td {
      padding: 10px 0;
      border-bottom: 1px solid #f1f5f9;
      font-size: 14px;
    }

    .detail-table tr:last-child td {
      border-bottom: none;
    }

    .detail-table .label {
      color: #94a3b8;
      width: 40%;
    }

    .detail-table .value {
      color: #1e293b;
      font-weight: 600;
    }

    .badge {
      display: inline-block;
      padding: 3px 10px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
    }

    .badge-red {
      background: #fef2f2;
      color: #dc2626;
    }

    .badge-green {
      background: #ecfdf5;
      color: #059669;
    }

    .badge-yellow {
      background: #fffbeb;
      color: #d97706;
    }

    .badge-blue {
      background: #eff6ff;
      color: #3b82f6;
    }

    .alert-box {
      border-radius: 10px;
      padding: 16px 20px;
      margin-bottom: 20px;
    }

    .alert-danger {
      background: #fef2f2;
      border: 1px solid #fca5a5;
    }

    .alert-warning {
      background: #fffbeb;
      border: 1px solid #fcd34d;
    }

    .alert-success {
      background: #ecfdf5;
      border: 1px solid #6ee7b7;
    }

    .alert-info {
      background: #eef2ff;
      border: 1px solid #a5b4fc;
    }

    .alert-box p {
      font-size: 14px;
      margin: 0;
    }

    .footer-text {
      font-size: 12px;
      color: #94a3b8;
      text-align: center;
      line-height: 1.6;
    }

    .footer-text a {
      color: #6366f1;
      text-decoration: none;
    }

    .divider {
      height: 1px;
      background: #f1f5f9;
      margin: 24px 0;
    }

    .mono {
      font-family: 'Courier New', Courier, monospace;
      font-size: 13px;
      background: #f1f5f9;
      padding: 2px 6px;
      border-radius: 4px;
      color: #4f46e5;
    }
  </style>
</head>

<body>
  <div class="wrapper">

    {{-- Header --}}
    <div class="header">
      <div class="logo">
        <div class="logo-icon">
          <svg width="20" height="20" fill="none" stroke="white" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
          </svg>
        </div>
        <span class="logo-text">Chargeback Shield</span>
      </div>
    </div>

    {{-- Card --}}
    <div class="card">
      <div class="card-header">
        @yield('header')
      </div>
      <div class="card-body">
        @yield('body')
      </div>
      @hasSection('footer')
      <div class="card-footer">
        @yield('footer')
      </div>
      @endif
    </div>

    {{-- Footer --}}
    <div style="margin-top: 24px;">
      <p class="footer-text">
        You received this email because you have a Chargeback Shield account.<br>
        <a href="{{ config('app.url') }}/app/settings">Manage notification preferences</a>
        &nbsp;·&nbsp;
        <a href="{{ config('app.url') }}">chargebackshield.io</a>
      </p>
    </div>

  </div>
</body>

</html>
