@extends('emails.layout')

@section('header')
<h1>Welcome to Chargeback Shield </h1>
<p style="margin:0; color:#64748b; font-size:14px;">
  Your account is ready. Let's get you integrated.
</p>
@endsection

@section('body')
<p>Hi {{ $merchant->company_name }},</p>

<p>
  Your Chargeback Shield account is live. Every card transaction you
  send through our API will now be scored, have evidence locked, and
  be ready for dispute in seconds.
</p>

<div class="alert-box alert-info">
  <p>
    <strong style="color:#3730a3;">Your API key is ready.</strong>
    Find it in your dashboard under Settings. Keep it secret —
    it authenticates every API call.
  </p>
</div>

<h2>Get integrated in 3 steps</h2>

<table class="detail-table">
  <tr>
    <td class="label">Step 1</td>
    <td class="value">Copy your API key from the dashboard</td>
  </tr>
  <tr>
    <td class="label">Step 2</td>
    <td class="value">
      Call <span class="mono">POST /api/v1/transaction/intercept</span>
      for every card transaction
    </td>
  </tr>
  <tr>
    <td class="label">Step 3</td>
    <td class="value">Read the decision — allow, step_up, or decline</td>
  </tr>
</table>

<div style="text-align: center; margin: 28px 0 8px;">
  <a href="{{ config('app.url') }}/app/dashboard" class="btn btn-primary">
    Go to dashboard →
  </a>
</div>

<div class="divider"></div>

<p style="font-size:13px; color:#94a3b8;">
  Need help? Read the
  <a href="{{ config('app.url') }}/docs" style="color:#6366f1;">integration docs</a>
  or reply to this email.
</p>
@endsection
