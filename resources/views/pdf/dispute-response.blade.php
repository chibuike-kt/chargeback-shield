<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Helvetica Neue', Arial, sans-serif;
      font-size: 11px;
      color: #1e293b;
      line-height: 1.5;
      padding: 40px;
      background: #ffffff;
    }

    .header {
      border-bottom: 3px solid #4f46e5;
      padding-bottom: 20px;
      margin-bottom: 28px;
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
    }

    .logo-area h1 {
      font-size: 18px;
      font-weight: 700;
      color: #4f46e5;
    }

    .logo-area p {
      font-size: 10px;
      color: #64748b;
      margin-top: 2px;
    }

    .doc-meta {
      text-align: right;
      font-size: 10px;
      color: #64748b;
    }

    .doc-meta strong {
      color: #1e293b;
      font-size: 11px;
      display: block;
      margin-bottom: 4px;
    }

    .badge {
      display: inline-block;
      padding: 3px 10px;
      border-radius: 20px;
      font-size: 10px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .badge-responded {
      background: #fffbeb;
      color: #92400e;
      border: 1px solid #fcd34d;
    }

    .badge-visa {
      background: #eff6ff;
      color: #1d4ed8;
      border: 1px solid #bfdbfe;
    }

    .badge-mastercard {
      background: #fff7ed;
      color: #c2410c;
      border: 1px solid #fed7aa;
    }

    .section {
      margin-bottom: 22px;
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      overflow: hidden;
    }

    .section-header {
      background: #f1f5f9;
      padding: 8px 16px;
      border-bottom: 1px solid #e2e8f0;
    }

    .section-header h2 {
      font-size: 11px;
      font-weight: 700;
      color: #374151;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .section-body {
      padding: 14px 16px;
    }

    .grid-2 {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
    }

    .field label {
      display: block;
      font-size: 9px;
      color: #94a3b8;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 2px;
    }

    .field value {
      display: block;
      font-size: 11px;
      color: #1e293b;
      font-weight: 500;
    }

    .field value.mono {
      font-family: 'Courier New', monospace;
      font-size: 10px;
    }

    .strategy-box {
      background: #eef2ff;
      border: 1px solid #c7d2fe;
      border-left: 4px solid #4f46e5;
      border-radius: 6px;
      padding: 12px 14px;
      margin-bottom: 14px;
    }

    .strategy-box h3 {
      font-size: 10px;
      font-weight: 700;
      color: #4f46e5;
      margin-bottom: 6px;
    }

    .strategy-box p {
      font-size: 10px;
      color: #374151;
      line-height: 1.6;
    }

    .signal-table {
      width: 100%;
      border-collapse: collapse;
    }

    .signal-table th {
      text-align: left;
      font-size: 9px;
      font-weight: 600;
      color: #64748b;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      padding: 6px 8px;
      border-bottom: 1px solid #e2e8f0;
      background: #f8fafc;
    }

    .signal-table td {
      padding: 7px 8px;
      font-size: 10px;
      color: #374151;
      border-bottom: 1px solid #f1f5f9;
    }

    .signal-table tr:last-child td {
      border-bottom: none;
    }

    .score-bar-bg {
      background: #e2e8f0;
      border-radius: 4px;
      height: 6px;
      width: 80px;
      display: inline-block;
      vertical-align: middle;
    }

    .score-bar-fill {
      height: 6px;
      border-radius: 4px;
      display: inline-block;
    }

    .valid-sig {
      background: #ecfdf5;
      color: #065f46;
      border: 1px solid #6ee7b7;
      padding: 2px 8px;
      border-radius: 20px;
      font-size: 9px;
      font-weight: 600;
    }

    .summary-box {
      background: #f0fdf4;
      border: 1px solid #86efac;
      border-radius: 6px;
      padding: 12px 14px;
      margin-bottom: 22px;
    }

    .summary-box p {
      font-size: 10px;
      color: #166534;
      line-height: 1.7;
    }

    .footer {
      border-top: 1px solid #e2e8f0;
      padding-top: 16px;
      margin-top: 28px;
      display: flex;
      justify-content: space-between;
      font-size: 9px;
      color: #94a3b8;
    }
  </style>
</head>

<body>

  {{-- Header --}}
  <div class="header">
    <div class="logo-area">
      <h1>Chargeback Shield</h1>
      <p>by Atlas Tech — Real-time chargeback protection for African fintechs</p>
      <div style="margin-top: 8px;">
        <span class="badge badge-responded">Dispute Response</span>
        &nbsp;
        <span class="badge badge-{{ strtolower($doc['dispute']['network']) }}">
          {{ $doc['dispute']['network'] }}
        </span>
      </div>
    </div>
    <div class="doc-meta">
      <strong>Dispute Response Document</strong>
      Dispute ID: {{ $doc['dispute']['id'] }}<br>
      Reason Code: {{ $doc['dispute']['reason_code'] }}<br>
      Generated: {{ \Carbon\Carbon::parse($doc['generated_at'])->format('M d, Y H:i:s') }} UTC<br>
      Document Version: {{ $doc['document_version'] }}
    </div>
  </div>

  {{-- Summary --}}
  <div class="summary-box">
    <p>{{ $doc['summary'] }}</p>
  </div>

  {{-- Dispute Details --}}
  <div class="section">
    <div class="section-header">
      <h2>Dispute Information</h2>
    </div>
    <div class="section-body">
      <div class="grid-2">
        <div class="field">
          <label>Reason Code</label>
          <value>{{ $doc['dispute']['reason_code'] }} — {{ $doc['dispute']['description'] }}</value>
        </div>
        <div class="field">
          <label>Network</label>
          <value>{{ $doc['dispute']['network'] }}</value>
        </div>
        <div class="field">
          <label>Filed At</label>
          <value>{{ $doc['dispute']['filed_at'] ? \Carbon\Carbon::parse($doc['dispute']['filed_at'])->format('M d, Y H:i') : 'N/A' }}</value>
        </div>
        <div class="field">
          <label>Response Time Limit</label>
          <value>{{ $doc['time_limit_days'] }} days from filing date</value>
        </div>
      </div>
    </div>
  </div>

  {{-- Transaction Details --}}
  <div class="section">
    <div class="section-header">
      <h2>Transaction Details</h2>
    </div>
    <div class="section-body">
      <div class="grid-2">
        <div class="field">
          <label>Transaction ID</label>
          <value class="mono">{{ $doc['transaction']['id'] }}</value>
        </div>
        <div class="field">
          <label>Amount</label>
          <value>{{ $doc['transaction']['formatted_amount'] }}</value>
        </div>
        <div class="field">
          <label>Processed At</label>
          <value>{{ \Carbon\Carbon::parse($doc['transaction']['processed_at'])->format('M d, Y H:i:s') }} UTC</value>
        </div>
        <div class="field">
          <label>Risk Decision</label>
          <value>{{ ucfirst($doc['transaction']['decision']) }} (Score: {{ $doc['transaction']['risk_score'] }})</value>
        </div>
      </div>
    </div>
  </div>

  {{-- Response Strategy --}}
  <div class="strategy-box">
    <h3>Response Strategy</h3>
    <p>{{ $doc['response_strategy'] }}</p>
  </div>

  <div class="strategy-box" style="background: #f0fdf4; border-color: #86efac; border-left-color: #059669;">
    <h3 style="color: #059669;">Winning Argument</h3>
    <p style="color: #166534;">{{ $doc['winning_argument'] }}</p>
  </div>

  {{-- Evidence Bundle --}}
  <div class="section">
    <div class="section-header">
      <h2>Cryptographic Evidence Bundle</h2>
    </div>
    <div class="section-body">
      <div class="grid-2" style="margin-bottom: 12px;">
        <div class="field">
          <label>Bundle ID</label>
          <value class="mono">{{ $doc['evidence']['bundle_id'] }}</value>
        </div>
        <div class="field">
          <label>Signature Status</label>
          <value>
            @if($doc['evidence']['signature_valid'])
            <span class="valid-sig">✓ HMAC-SHA256 VERIFIED</span>
            @else
            <span style="color: #dc2626; font-weight: 600;">✗ SIGNATURE INVALID</span>
            @endif
          </value>
        </div>
        <div class="field">
          <label>Evidence Locked At</label>
          <value>{{ \Carbon\Carbon::parse($doc['evidence']['locked_at'])->format('M d, Y H:i:s') }} UTC</value>
        </div>
        <div class="field">
          <label>HMAC Signature</label>
          <value class="mono" style="font-size: 8px; word-break: break-all;">
            {{ substr($doc['evidence']['hmac_signature'], 0, 32) }}...
          </value>
        </div>
      </div>

      {{-- Device & Network --}}
      <div class="grid-2">
        <div class="field">
          <label>Device Fingerprint</label>
          <value class="mono">{{ $doc['evidence']['payload']['device']['fingerprint'] ?? 'N/A' }}</value>
        </div>
        <div class="field">
          <label>IP Address / Country</label>
          <value>{{ $doc['evidence']['payload']['network']['ip_address'] ?? 'N/A' }} / {{ $doc['evidence']['payload']['network']['ip_country'] ?? 'N/A' }}</value>
        </div>
        <div class="field">
          <label>Card Country</label>
          <value>{{ $doc['evidence']['payload']['card']['country'] ?? 'N/A' }}</value>
        </div>
        <div class="field">
          <label>Session Age</label>
          <value>{{ $doc['evidence']['payload']['device']['session_age_seconds'] ?? 0 }} seconds</value>
        </div>
      </div>
    </div>
  </div>

  {{-- Risk Signals --}}
  <div class="section">
    <div class="section-header">
      <h2>Risk Signal Breakdown (at time of authorization)</h2>
    </div>
    <div class="section-body">
      <table class="signal-table">
        <thead>
          <tr>
            <th>Signal</th>
            <th>Value</th>
            <th>Score</th>
            <th>Weight</th>
            <th>Contribution</th>
          </tr>
        </thead>
        <tbody>
          @foreach($doc['risk_signals'] as $signal)
          <tr>
            <td><strong>{{ str_replace('_', ' ', ucfirst($signal['signal'])) }}</strong></td>
            <td>{{ $signal['value'] }}</td>
            <td>
              @php
              $pct = $signal['score'] * 100;
              $color = $signal['score'] < 0.4 ? '#059669' : ($signal['score'] < 0.7 ? '#d97706' : '#dc2626' );
                @endphp
                <div class="score-bar-bg">
                <div class="score-bar-fill" style="width: {{ $pct }}%; background: {{ $color }}; height: 6px;"></div>
    </div>
    <span style="margin-left: 6px; color: {{ $color }}; font-weight: 600;">{{ number_format($signal['score'], 3) }}</span>
    </td>
    <td>{{ $signal['weight'] * 100 }}%</td>
    <td>{{ number_format($signal['contribution'], 4) }}</td>
    </tr>
    @endforeach
    </tbody>
    </table>
    <div style="margin-top: 10px; text-align: right; font-size: 10px; color: #374151;">
      <strong>Composite Risk Score: {{ $doc['transaction']['risk_score'] }} ({{ ucfirst($doc['transaction']['risk_level']) }} Risk)</strong>
    </div>
  </div>
  </div>

  {{-- Footer --}}
  <div class="footer">
    <span>Generated by Chargeback Shield — Atlas Tech</span>
    <span>Document ID: {{ $doc['dispute']['id'] }} | {{ \Carbon\Carbon::parse($doc['generated_at'])->format('Y-m-d H:i:s') }} UTC</span>
    <span>CONFIDENTIAL — For dispute submission only</span>
  </div>

</body>

</html>
