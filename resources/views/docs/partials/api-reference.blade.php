<h2 id="intercept">Intercept transaction</h2>

<div class="flex items-center gap-3 mb-4">
  <span class="method-badge method-post">POST</span>
  <span class="inline-code">/api/v1/transaction/intercept</span>
</div>

<p>
  The core endpoint. Call this for every card transaction before processing.
  Returns a risk score, decision, and evidence bundle ID in under 100ms.
</p>

<h3>Request body</h3>

<table class="param-table w-full border border-slate-200 rounded-xl overflow-hidden mb-5">
  <thead>
    <tr>
      <th>Parameter</th>
      <th>Type</th>
      <th>Required</th>
      <th>Description</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><span class="inline-code">idempotency_key</span></td>
      <td class="text-slate-500 text-xs">string</td>
      <td class="text-emerald-600 text-xs font-semibold">Required</td>
      <td class="text-slate-600 text-sm">Unique key per transaction attempt. Min 8 chars.</td>
    </tr>
    <tr>
      <td><span class="inline-code">card_bin</span></td>
      <td class="text-slate-500 text-xs">string</td>
      <td class="text-emerald-600 text-xs font-semibold">Required</td>
      <td class="text-slate-600 text-sm">First 6 digits of the card number.</td>
    </tr>
    <tr>
      <td><span class="inline-code">card_last4</span></td>
      <td class="text-slate-500 text-xs">string</td>
      <td class="text-emerald-600 text-xs font-semibold">Required</td>
      <td class="text-slate-600 text-sm">Last 4 digits of the card number.</td>
    </tr>
    <tr>
      <td><span class="inline-code">card_country</span></td>
      <td class="text-slate-500 text-xs">string</td>
      <td class="text-slate-400 text-xs">Optional</td>
      <td class="text-slate-600 text-sm">2-letter ISO country code of card issuer. e.g. <span class="inline-code">NG</span></td>
    </tr>
    <tr>
      <td><span class="inline-code">amount</span></td>
      <td class="text-slate-500 text-xs">integer</td>
      <td class="text-emerald-600 text-xs font-semibold">Required</td>
      <td class="text-slate-600 text-sm">Transaction amount in minor units. e.g. <span class="inline-code">500000</span> = NGN 5,000.00</td>
    </tr>
    <tr>
      <td><span class="inline-code">currency</span></td>
      <td class="text-slate-500 text-xs">string</td>
      <td class="text-emerald-600 text-xs font-semibold">Required</td>
      <td class="text-slate-600 text-sm">3-letter ISO currency code. e.g. <span class="inline-code">NGN</span></td>
    </tr>
    <tr>
      <td><span class="inline-code">ip_address</span></td>
      <td class="text-slate-500 text-xs">string</td>
      <td class="text-slate-400 text-xs">Optional</td>
      <td class="text-slate-600 text-sm">IPv4 or IPv6 address of the cardholder.</td>
    </tr>
    <tr>
      <td><span class="inline-code">ip_country</span></td>
      <td class="text-slate-500 text-xs">string</td>
      <td class="text-slate-400 text-xs">Optional</td>
      <td class="text-slate-600 text-sm">2-letter ISO country code from IP geolocation.</td>
    </tr>
    <tr>
      <td><span class="inline-code">device_fingerprint</span></td>
      <td class="text-slate-500 text-xs">string</td>
      <td class="text-slate-400 text-xs">Optional</td>
      <td class="text-slate-600 text-sm">Unique device identifier. Missing fingerprints increase risk score.</td>
    </tr>
    <tr>
      <td><span class="inline-code">session_age_seconds</span></td>
      <td class="text-slate-500 text-xs">integer</td>
      <td class="text-slate-400 text-xs">Optional</td>
      <td class="text-slate-600 text-sm">Age of the current user session in seconds. 0 = brand new session.</td>
    </tr>
    <tr>
      <td><span class="inline-code">merchant_category</span></td>
      <td class="text-slate-500 text-xs">string</td>
      <td class="text-slate-400 text-xs">Optional</td>
      <td class="text-slate-600 text-sm">4-digit Merchant Category Code (MCC). e.g. <span class="inline-code">5411</span></td>
    </tr>
  </tbody>
</table>

<h2 id="score">Score transaction (post-auth)</h2>

<div class="flex items-center gap-3 mb-4">
  <span class="method-badge method-post">POST</span>
  <span class="inline-code">/api/v1/transaction/score</span>
</div>

<p>
  Post-authorization scoring endpoint. Call this after your payment flow
  has already approved the transaction. Fire and forget — you don't need
  to wait for the response or act on it. Evidence is locked automatically
  for every transaction regardless of score.
</p>

<div class="callout callout-success">
  This endpoint adds <strong>zero latency</strong> to your payment flow.
  Call it asynchronously and let it run in the background.
</div>

<h3>Request body</h3>

<p>Same parameters as <span class="inline-code">POST /transaction/intercept</span>,
  plus one optional field:</p>

<table class="param-table w-full border border-slate-200 rounded-xl overflow-hidden mb-5">
  <thead>
    <tr>
      <th>Parameter</th>
      <th>Type</th>
      <th>Required</th>
      <th>Description</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><span class="inline-code">external_reference</span></td>
      <td class="text-slate-500 text-xs">string</td>
      <td class="text-slate-400 text-xs">Optional</td>
      <td class="text-slate-600 text-sm">Your internal transaction reference for cross-referencing.</td>
    </tr>
  </tbody>
</table>

<h3>Response</h3>

<div class="code-block">
  <pre>{
  <span class="key">"success"</span>: <span class="value">true</span>,
  <span class="key">"message"</span>: <span class="string">"Transaction scored and evidence locked."</span>,
  <span class="key">"data"</span>: {
    <span class="key">"transaction_id"</span>:      <span class="string">"01kkzedkjzwxkjmkefbehe2tdh"</span>,
    <span class="key">"risk_score"</span>:          <span class="value">0.1240</span>,
    <span class="key">"risk_level"</span>:          <span class="string">"low"</span>,
    <span class="key">"evidence_bundle_id"</span>:  <span class="string">"01kkzedkk0abc123def456gh"</span>,
    <span class="key">"high_risk_detected"</span>:  <span class="value">false</span>,
    <span class="key">"signals"</span>:             [...],
    <span class="key">"scored_at"</span>:           <span class="string">"2026-03-18T03:04:24+00:00"</span>,
    <span class="key">"idempotent"</span>:          <span class="value">false</span>
  }
}</pre>
</div>

<div class="callout callout-warning">
  When <span class="inline-code">high_risk_detected</span> is
  <span class="inline-code">true</span>, a
  <span class="inline-code">transaction.high_risk_detected</span>
  webhook has been fired to your endpoint. Your system should
  take appropriate action — freeze card, flag account, trigger review.
</div>

<h3>Response</h3>

<div class="code-block">
  <pre>{
  <span class="key">"success"</span>: <span class="value">true</span>,
  <span class="key">"message"</span>: <span class="string">"Transaction approved."</span>,
  <span class="key">"data"</span>: {
    <span class="key">"transaction_id"</span>:    <span class="string">"01kkzedkjzwxkjmkefbehe2tdh"</span>,
    <span class="key">"decision"</span>:          <span class="string">"allow"</span>,
    <span class="key">"risk_score"</span>:        <span class="value">0.1240</span>,
    <span class="key">"risk_level"</span>:        <span class="string">"low"</span>,
    <span class="key">"status"</span>:            <span class="string">"approved"</span>,
    <span class="key">"currency"</span>:          <span class="string">"NGN"</span>,
    <span class="key">"amount"</span>:            <span class="value">500000</span>,
    <span class="key">"evidence_bundle_id"</span>: <span class="string">"01kkzedkk0abc123def456gh"</span>,
    <span class="key">"signals"</span>: [
      {
        <span class="key">"signal_name"</span>:          <span class="string">"velocity"</span>,
        <span class="key">"raw_value"</span>:           <span class="string">"tx_hour:1 spend_24h:5000"</span>,
        <span class="key">"normalized_score"</span>:    <span class="value">0.0500</span>,
        <span class="key">"weight"</span>:             <span class="value">0.25</span>,
        <span class="key">"weighted_contribution"</span>: <span class="value">0.0125</span>
      }
      <span class="comment">// ... 5 more signals</span>
    ],
    <span class="key">"processed_at"</span>: <span class="string">"2026-03-18T03:04:24+00:00"</span>,
    <span class="key">"idempotent"</span>:    <span class="value">false</span>
  }
}</pre>
</div>

<h3>Decision values</h3>

<table class="param-table w-full border border-slate-200 rounded-xl overflow-hidden mb-6">
  <thead>
    <tr>
      <th>Decision</th>
      <th>Score range</th>
      <th>What to do</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><span class="inline-code text-emerald-600">allow</span></td>
      <td class="text-slate-600 text-sm">0.00 – 0.39</td>
      <td class="text-slate-600 text-sm">Proceed with the transaction</td>
    </tr>
    <tr>
      <td><span class="inline-code text-amber-600">step_up</span></td>
      <td class="text-slate-600 text-sm">0.40 – 0.69</td>
      <td class="text-slate-600 text-sm">Trigger 3DS authentication before proceeding</td>
    </tr>
    <tr>
      <td><span class="inline-code text-red-600">decline</span></td>
      <td class="text-slate-600 text-sm">0.70 – 1.00</td>
      <td class="text-slate-600 text-sm">Block the transaction</td>
    </tr>
  </tbody>
</table>

{{-- Get transaction --}}
<h2 id="get-transaction">Get transaction</h2>

<div class="flex items-center gap-3 mb-4">
  <span class="method-badge method-get">GET</span>
  <span class="inline-code">/api/v1/transaction/:id</span>
</div>

<p>Retrieve a transaction by its ULID. Returns full details including all risk signal logs.</p>

{{-- Get evidence --}}
<h2 id="get-evidence">Get evidence bundle</h2>

<div class="flex items-center gap-3 mb-4">
  <span class="method-badge method-get">GET</span>
  <span class="inline-code">/api/v1/transaction/:id/evidence</span>
</div>

<p>
  Retrieve and decrypt the evidence bundle for an approved transaction.
  The bundle is decrypted on the fly and the HMAC signature is verified on every retrieval.
</p>

<div class="code-block">
  <pre>{
  <span class="key">"data"</span>: {
    <span class="key">"bundle_id"</span>:       <span class="string">"01kkzedkk0abc123def456gh"</span>,
    <span class="key">"transaction_id"</span>:  <span class="string">"01kkzedkjzwxkjmkefbehe2tdh"</span>,
    <span class="key">"signature_valid"</span>: <span class="value">true</span>,
    <span class="key">"hmac_signature"</span>:  <span class="string">"a1b2c3d4e5f6..."</span>,
    <span class="key">"created_at"</span>:      <span class="string">"2026-03-18T03:04:24+00:00"</span>,
    <span class="key">"payload"</span>: {
      <span class="key">"evidence_version"</span>: <span class="string">"1.0"</span>,
      <span class="key">"transaction"</span>: { <span class="comment">/* full transaction context */</span> },
      <span class="key">"card"</span>:        { <span class="comment">/* card details */</span> },
      <span class="key">"network"</span>:     { <span class="comment">/* IP, geolocation */</span> },
      <span class="key">"device"</span>:      { <span class="comment">/* fingerprint, session */</span> },
      <span class="key">"risk"</span>:        { <span class="comment">/* score, signals */</span> },
      <span class="key">"locked_at"</span>:   <span class="string">"2026-03-18T03:04:24+00:00"</span>
    }
  }
}</pre>
</div>

{{-- File dispute --}}
<h2 id="file-dispute">File a dispute</h2>

<div class="flex items-center gap-3 mb-4">
  <span class="method-badge method-post">POST</span>
  <span class="inline-code">/api/v1/dispute</span>
</div>

<p>
  Call this the moment a chargeback notification lands. Chargeback Shield retrieves
  the locked evidence bundle, verifies the signature, and generates a complete
  dispute response document instantly.
</p>

<table class="param-table w-full border border-slate-200 rounded-xl overflow-hidden mb-4">
  <thead>
    <tr>
      <th>Parameter</th>
      <th>Type</th>
      <th>Required</th>
      <th>Description</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><span class="inline-code">transaction_id</span></td>
      <td class="text-slate-500 text-xs">string</td>
      <td class="text-emerald-600 text-xs font-semibold">Required</td>
      <td class="text-slate-600 text-sm">The ULID of the disputed transaction.</td>
    </tr>
    <tr>
      <td><span class="inline-code">reason_code</span></td>
      <td class="text-slate-500 text-xs">string</td>
      <td class="text-emerald-600 text-xs font-semibold">Required</td>
      <td class="text-slate-600 text-sm">Visa or Mastercard reason code. e.g. <span class="inline-code">4863</span></td>
    </tr>
    <tr>
      <td><span class="inline-code">network</span></td>
      <td class="text-slate-500 text-xs">string</td>
      <td class="text-emerald-600 text-xs font-semibold">Required</td>
      <td class="text-slate-600 text-sm"><span class="inline-code">visa</span> or <span class="inline-code">mastercard</span></td>
    </tr>
    <tr>
      <td><span class="inline-code">filed_at</span></td>
      <td class="text-slate-500 text-xs">datetime</td>
      <td class="text-slate-400 text-xs">Optional</td>
      <td class="text-slate-600 text-sm">When the chargeback was filed. Defaults to now.</td>
    </tr>
  </tbody>
</table>

{{-- Get / list disputes --}}
<h2 id="get-dispute">Get dispute response</h2>

<div class="flex items-center gap-3 mb-4">
  <span class="method-badge method-get">GET</span>
  <span class="inline-code">/api/v1/dispute/:id/response</span>
</div>

<p>Returns the generated dispute response document for a given dispute ID.</p>

<h2 id="list-disputes">List disputes</h2>

<div class="flex items-center gap-3 mb-4">
  <span class="method-badge method-get">GET</span>
  <span class="inline-code">/api/v1/disputes</span>
</div>

<p>Returns a paginated list of all disputes for the authenticated merchant.</p>
