<h2 id="quickstart">Quickstart</h2>

<p>
  Get from zero to your first scored transaction in under 15 minutes.
  No SDK required — a single HTTP call is all it takes.
</p>

<h3>Step 1 — Create your account</h3>
<p>
  Sign up at <a href="/app/register">chargebackshield.io/app/register</a>.
  Your API key and webhook secret are generated automatically on registration.
  They look like this:
</p>

<div class="code-block">
  <pre><span class="comment"># API Key (use in X-API-Key header)</span>
<span class="string">cs_live_a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6</span>

<span class="comment"># Webhook Secret (use to verify webhook signatures)</span>
<span class="string">whsec_a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6</span></pre>
</div>

<h3>Step 2 — Intercept your first transaction</h3>
<p>
  Call <span class="inline-code">POST /api/v1/transaction/intercept</span> every time
  a card transaction is initiated in your system. Pass the card details,
  amount, and device metadata.
</p>

<div class="code-block">
  <pre><span class="comment"># cURL</span>
<span class="keyword">curl</span> -X POST https://api.chargebackshield.io/api/v1/transaction/intercept \
  -H <span class="string">"X-API-Key: cs_live_your_key_here"</span> \
  -H <span class="string">"Content-Type: application/json"</span> \
  -d <span class="string">'{
    "idempotency_key": "order_12345_attempt_1",
    "card_bin": "459234",
    "card_last4": "4242",
    "card_country": "NG",
    "amount": 500000,
    "currency": "NGN",
    "ip_address": "197.210.1.1",
    "ip_country": "NG",
    "device_fingerprint": "fp_abc123def456",
    "session_age_seconds": 900,
    "merchant_category": "5411"
  }'</span></pre>
</div>

<h3>Step 3 — Read the decision</h3>
<p>
  The response comes back in under 100ms with a decision, risk score, and evidence bundle ID.
</p>

<div class="code-block">
  <pre><span class="comment"># Response</span>
{
  <span class="key">"success"</span>: <span class="value">true</span>,
  <span class="key">"data"</span>: {
    <span class="key">"transaction_id"</span>: <span class="string">"01kkzedkjzwxkjmkefbehe2tdh"</span>,
    <span class="key">"decision"</span>:       <span class="string">"allow"</span>,
    <span class="key">"risk_score"</span>:     <span class="value">0.124</span>,
    <span class="key">"risk_level"</span>:     <span class="string">"low"</span>,
    <span class="key">"status"</span>:         <span class="string">"approved"</span>,
    <span class="key">"evidence_bundle_id"</span>: <span class="string">"01kkzedkk0abc123def456gh"</span>,
    <span class="key">"processed_at"</span>:   <span class="string">"2026-03-18T03:04:24+00:00"</span>
  }
}</pre>
</div>

<h3>Step 4 — Act on the decision</h3>

<div class="code-block">
  <pre><span class="comment">// Node.js example</span>
<span class="keyword">const</span> result = await interceptTransaction(data);

<span class="keyword">if</span> (result.decision === <span class="string">'decline'</span>) {
  <span class="keyword">return</span> res.status(<span class="value">402</span>).json({ error: <span class="string">'Transaction declined'</span> });
}

<span class="keyword">if</span> (result.decision === <span class="string">'step_up'</span>) {
  <span class="keyword">return</span> trigger3DS(result.transaction_id);
}

<span class="comment">// decision === 'allow' — proceed with payment</span>
<span class="keyword">await</span> processPayment(data);</pre>
</div>

<div class="callout callout-success">
  <strong>You are live.</strong> Every approved transaction now has a cryptographic evidence
  bundle locked automatically. If a chargeback lands, the response is already built.
</div>

<h3 id="idempotency">Idempotency</h3>
<p>
  Every intercept request requires an <span class="inline-code">idempotency_key</span>.
  If you send the same key twice, the original response is returned without reprocessing.
  Use your internal order or transaction ID as the key.
</p>

<div class="callout callout-info">
  Idempotency keys are cached for <strong>24 hours</strong>. Use a unique key per transaction attempt,
  not per order — retries of the same attempt should use the same key.
</div>

<h3 id="environments">Environments</h3>

<table class="param-table w-full border border-slate-200 rounded-xl overflow-hidden mb-4">
  <thead>
    <tr>
      <th>Environment</th>
      <th>Base URL</th>
      <th>Key prefix</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><span class="inline-code">live</span></td>
      <td><span class="inline-code">https://api.chargebackshield.io/api/v1</span></td>
      <td><span class="inline-code">cs_live_</span></td>
    </tr>
    <tr>
      <td><span class="inline-code">test</span></td>
      <td><span class="inline-code">http://localhost:8000/api/v1</span></td>
      <td><span class="inline-code">cs_live_</span> (same key, local)</td>
    </tr>
  </tbody>
</table>
