@extends('emails.layout')

@section('header')
<h1>Your weekly summary</h1>
<p style="margin:0; color:#64748b; font-size:14px;">
  {{ now()->subDays(7)->format('M d') }} –
  {{ now()->format('M d, Y') }}
</p>
@endsection

@section('body')
<p>Hi {{ $merchant->company_name }},</p>

<p>
  Here's what happened across your transactions this week.
</p>

{{-- Stats --}}
<div class="stat-row">
  <div class="stat-box">
    <span class="stat-value">{{ number_format($stats['total_transactions']) }}</span>
    <span class="stat-label">Transactions</span>
  </div>
  <div class="stat-box">
    <span class="stat-value" style="color:#dc2626;">
      {{ number_format($stats['chargebacks']) }}
    </span>
    <span class="stat-label">Chargebacks</span>
  </div>
  <div class="stat-box">
    <span class="stat-value" style="color:#059669;">
      {{ number_format($stats['disputes_won']) }}
    </span>
    <span class="stat-label">Disputes Won</span>
  </div>
</div>

<div class="stat-row">
  <div class="stat-box">
    <span class="stat-value" style="color:#d97706;">
      {{ number_format($stats['flagged']) }}
    </span>
    <span class="stat-label">Flagged (3DS)</span>
  </div>
  <div class="stat-box">
    <span class="stat-value" style="color:#dc2626;">
      {{ number_format($stats['declined']) }}
    </span>
    <span class="stat-label">Declined</span>
  </div>
  <div class="stat-box">
    <span class="stat-value">
      {{ $stats['total_transactions'] > 0
                    ? number_format(($stats['declined'] / $stats['total_transactions']) * 100, 1)
                    : 0 }}%
    </span>
    <span class="stat-label">Decline Rate</span>
  </div>
</div>

@if($stats['chargebacks'] > 0)
<div class="alert-box alert-danger">
  <p>
    <strong style="color:#991b1b;">
      {{ $stats['chargebacks'] }} chargeback{{ $stats['chargebacks'] > 1 ? 's' : '' }}
      this week.
    </strong>
    @if($stats['disputes_won'] > 0)
    You won {{ $stats['disputes_won'] }} of them.
    Keep submitting your evidence responses promptly.
    @else
    Make sure you respond to open disputes before the deadline.
    @endif
  </p>
</div>
@else
<div class="alert-box alert-success">
  <p>
    <strong style="color:#065f46;">Clean week.</strong>
    No chargebacks filed against your transactions this week.
  </p>
</div>
@endif

<h2>Webhook health</h2>

<table class="detail-table">
  <tr>
    <td class="label">Delivered</td>
    <td class="value">{{ number_format($stats['webhooks_delivered']) }}</td>
  </tr>
  <tr>
    <td class="label">Failed</td>
    <td class="value">
      {{ number_format($stats['webhooks_failed']) }}
      @if($stats['webhooks_failed'] > 0)
      <span class="badge badge-red" style="margin-left:6px;">Needs attention</span>
      @endif
    </td>
  </tr>
  <tr>
    <td class="label">Success rate</td>
    <td class="value">
      @php
      $total = $stats['webhooks_delivered'] + $stats['webhooks_failed'];
      $rate = $total > 0
      ? round(($stats['webhooks_delivered'] / $total) * 100)
      : 100;
      @endphp
      <span class="badge {{ $rate >= 90 ? 'badge-green' : ($rate >= 70 ? 'badge-yellow' : 'badge-red') }}">
        {{ $rate }}%
      </span>
    </td>
  </tr>
</table>

<div style="text-align: center; margin: 28px 0 8px;">
  <a href="{{ config('app.url') }}/app/dashboard" class="btn btn-primary">
    View full dashboard →
  </a>
</div>
@endsection

@section('footer')
<p class="footer-text">
  Weekly summaries are sent every Monday morning.
  <a href="{{ config('app.url') }}/app/settings">Unsubscribe</a>
</p>
@endsection
