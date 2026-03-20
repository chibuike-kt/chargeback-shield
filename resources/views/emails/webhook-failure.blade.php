@extends('emails.layout')

@section('header')
<h1>Webhook permanently failed</h1>
<p style="margin:0; color:#64748b; font-size:14px;">
  A webhook delivery failed after 3 attempts and will not be retried automatically.
</p>
@endsection

@section('body')
<p>Hi {{ $merchant->company_name }},</p>

<p>
  A webhook event failed to deliver after 3 retry attempts.
  No further automatic retries will be made. You can manually
  re-trigger the delivery from your webhook log.
</p>

<div class="alert-box alert-warning">
  <p>
    <strong style="color:#92400e;">Check your endpoint.</strong>
    Make sure your webhook URL is reachable and returning a 2xx response.
  </p>
</div>

<h2>Delivery details</h2>

<table class="detail-table">
  <tr>
    <td class="label">Event type</td>
    <td class="value">
      <span class="mono">{{ $delivery->event_type->value }}</span>
    </td>
  </tr>
  <tr>
    <td class="label">Endpoint URL</td>
    <td class="value" style="word-break:break-all;">{{ $delivery->url }}</td>
  </tr>
  <tr>
    <td class="label">Last HTTP status</td>
    <td class="value">
      <span class="badge badge-red">
        {{ $delivery->http_status ?? 'No response' }}
      </span>
    </td>
  </tr>
  <tr>
    <td class="label">Attempts made</td>
    <td class="value">3 of 3</td>
  </tr>
  <tr>
    <td class="label">Last response</td>
    <td class="value" style="font-size:13px; color:#94a3b8;">
      {{ Str::limit($delivery->response_body ?? 'No response body', 100) }}
    </td>
  </tr>
  <tr>
    <td class="label">Delivery ID</td>
    <td class="value mono" style="font-size:12px;">{{ $delivery->ulid }}</td>
  </tr>
</table>

<div style="text-align: center; margin: 28px 0 8px;">
  <a href="{{ config('app.url') }}/app/webhooks"
    class="btn btn-primary">
    View webhook log →
  </a>
</div>

<p style="font-size:13px; color:#94a3b8; text-align:center; margin-top:16px;">
  You can manually re-trigger this delivery from the webhook log
  once your endpoint is fixed.
</p>
@endsection
