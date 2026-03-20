@extends('emails.layout')

@section('header')
<h1>Chargeback filed — response ready</h1>
<p style="margin:0; color:#64748b; font-size:14px;">
  A dispute has been filed against one of your transactions.
  Your response document is already built.
</p>
@endsection

@section('body')
<p>Hi {{ $merchant->company_name }},</p>

<p>
  A chargeback has been filed against transaction
  <span class="mono">{{ substr($dispute->transaction->ulid, 0, 20) }}...</span>
  Chargeback Shield has already retrieved the locked evidence bundle,
  verified the signature, and generated your dispute response document.
</p>

<div class="alert-box alert-danger">
  <p>
    <strong style="color:#991b1b;">Action required.</strong>
    You have a limited window to submit your dispute response
    to {{ strtoupper($dispute->network->value) }}.
    The response document is ready now.
  </p>
</div>

<h2>Dispute details</h2>

<table class="detail-table">
  <tr>
    <td class="label">Dispute ID</td>
    <td class="value mono" style="font-size:12px;">{{ $dispute->ulid }}</td>
  </tr>
  <tr>
    <td class="label">Reason code</td>
    <td class="value">
      {{ $dispute->reason_code }} —
      {{ $dispute->reason_description }}
    </td>
  </tr>
  <tr>
    <td class="label">Network</td>
    <td class="value">{{ strtoupper($dispute->network->value) }}</td>
  </tr>
  <tr>
    <td class="label">Transaction amount</td>
    <td class="value">
      {{ $dispute->transaction->currency }}
      {{ number_format($dispute->transaction->amount / 100, 2) }}
    </td>
  </tr>
  <tr>
    <td class="label">Transaction date</td>
    <td class="value">
      {{ $dispute->transaction->created_at->format('M d, Y H:i') }} UTC
    </td>
  </tr>
  <tr>
    <td class="label">Risk score at approval</td>
    <td class="value">
      {{ number_format($dispute->transaction->risk_score, 4) }}
      <span class="badge badge-green" style="margin-left:6px;">
        {{ ucfirst($dispute->transaction->risk_level->value) }} risk
      </span>
    </td>
  </tr>
  <tr>
    <td class="label">Evidence signature</td>
    <td class="value">
      @if($dispute->transaction->evidenceBundle?->is_verified)
      <span class="badge badge-green">HMAC-SHA256 Verified</span>
      @else
      <span class="badge badge-red">Not verified</span>
      @endif
    </td>
  </tr>
  <tr>
    <td class="label">Filed at</td>
    <td class="value">{{ $dispute->filed_at?->format('M d, Y H:i') }} UTC</td>
  </tr>
</table>

<div style="text-align: center; margin: 28px 0 8px;">
  <a href="{{ config('app.url') }}/app/disputes/{{ $dispute->ulid }}"
    class="btn btn-primary">
    View dispute response →
  </a>
</div>

<div style="text-align: center; margin-top: 12px;">
  <a href="{{ config('app.url') }}/app/disputes/{{ $dispute->ulid }}/pdf"
    class="btn btn-secondary">
    Download PDF response
  </a>
</div>
@endsection

@section('footer')
<p class="footer-text">
  This alert was triggered automatically when the chargeback was filed
  via the Chargeback Shield API. The dispute response was generated in
  under 1 second from the locked evidence bundle.
</p>
@endsection
