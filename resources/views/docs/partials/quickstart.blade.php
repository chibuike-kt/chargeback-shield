<h2 id="quickstart">Quickstart</h2>

<p>
  Get from zero to your first scored transaction in under 15 minutes.
  Two integration patterns are available — choose the one that fits
  your architecture.
</p>

<div class="callout callout-info">
  <strong>Which pattern should I use?</strong>
  Use <strong>post-auth scoring</strong> for most transactions — it never
  touches your approval latency. Use <strong>pre-auth interception</strong>
  for high-value transactions where you want to block before approving.
  Most fintechs use both together.
</div>

<h3>Step 1 — Create your account</h3>
<p>
  Sign up at <a href="/app/register">chargebackshield.io/app/register</a>.
  Your API key and webhook secret are generated automatically on registration.
</p>

<div class="code-block">
  <pre><span class="comment"># API Key</span>
<span class="string">cs_live_a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6</span>

<span class="comment"># Webhook Secret</span>
<span class="string">whsec_a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6</span></pre>
</div>

{{-- Pattern A --}}
<h2 id="pattern-post-auth">Pattern A — Post-auth scoring (recommended for volume)</h2>

<p>
  Your payment flow approves the transaction normally. You call Chargeback Shield
  <strong>after</strong> approval — fire and forget. Zero latency added.
  Evidence is locked, transaction is scored, and if the risk score is high
  a webhook fires to your endpoint immediately.
</p>

<div class="code-block">
  <pre><span class="comment">// Node.js — post-auth pattern</span>
<span class="keyword">async function</span> <span class="method">processTransaction</span>(txData) {

  <span class="comment">// Step 1: approve with your existing flow</span>
  <span class="keyword">const</span> approval = <span class="keyword">await</span> cardNetwork.<span class="method">authorize</span>(txData);

  <span class="comment">// Step 2: score in background — don't await, don't block</span>
  chargebackShield.transactions.<span class="method">score</span>({
    idempotencyKey:    txData.id,
    cardBin:           txData.card.bin,
    cardLast4:         txData.card.last4,
    cardCountry:       txData.card.country,
    amount:            txData.amount,
    currency:          txData.currency,
    ipAddress:         txData.ip,
    ipCountry:         txData.geoCountry,
    deviceFingerprint: txData.device.fingerprint,
    sessionAgeSeconds: txData.session.age,
    merchantCategory:  txData.mcc,
  }).<span class="method">catch</span>(console.error); <span class="comment">// never let this crash your flow</span>

  <span class="comment">// Step 3: return approval immediately</span>
  <span class="keyword">return</span> approval;
}</pre>
</div>

<div class="callout callout-success">
  This pattern adds <strong>zero milliseconds</strong> to your transaction
  approval time. 5,000 concurrent transactions? No problem.
  Chargeback Shield runs completely out of band.
</div>

<h3>Handling the high-risk webhook</h3>

<p>
  When a post-auth transaction scores above 0.70, Chargeback Shield fires a
  <span class="inline-code">transaction.high_risk_detected</span> webhook
  to your endpoint. Your system decides what to do — freeze the card,
  flag the account, trigger a review.
</p>

<div class="code-block">
  <pre><span class="comment">// Your webhook handler</span>
app.<span class="method">post</span>(<span class="string">'/webhooks/chargeback-shield'</span>, (req, res) => {
  <span class="keyword">const</span> event = req.body;

  <span class="keyword">if</span> (event.event === <span class="string">'transaction.high_risk_detected'</span>) {
    <span class="comment">// Transaction already approved — take action now</span>
    <span class="keyword">await</span> flagAccountForReview(event.transaction_id);
    <span class="keyword">await</span> notifyFraudTeam(event);

    <span class="comment">// Optional: reverse the transaction</span>
    <span class="keyword">if</span> (event.risk_score > <span class="value">0.85</span>) {
      <span class="keyword">await</span> cardNetwork.<span class="method">reverse</span>(event.transaction_id);
    }
  }

  res.<span class="method">json</span>({ received: <span class="value">true</span> });
});</pre>
</div>

{{-- Pattern B --}}
<h2 id="pattern-pre-auth">Pattern B — Pre-auth interception (high-value transactions)</h2>

<p>
  For transactions above your defined threshold, call Chargeback Shield
  <strong>before</strong> approving and act on the decision.
  This adds under 100ms to your approval flow for those transactions only.
</p>

<div class="code-block">
  <pre><span class="comment">// Node.js — hybrid pattern</span>
<span class="keyword">const</span> HIGH_VALUE_THRESHOLD = <span class="value">5000000</span>; <span class="comment">// NGN 50,000 in kobo</span>

<span class="keyword">async function</span> <span class="method">processTransaction</span>(txData) {

  <span class="keyword">if</span> (txData.amount >= HIGH_VALUE_THRESHOLD) {
    <span class="comment">// High value — check synchronously before approving</span>
    <span class="keyword">const</span> result = <span class="keyword">await</span> chargebackShield.transactions.<span class="method">intercept</span>({
      idempotencyKey:    txData.id,
      cardBin:           txData.card.bin,
      cardLast4:         txData.card.last4,
      cardCountry:       txData.card.country,
      amount:            txData.amount,
      currency:          txData.currency,
      ipAddress:         txData.ip,
      ipCountry:         txData.geoCountry,
      deviceFingerprint: txData.device.fingerprint,
      sessionAgeSeconds: txData.session.age,
    });

    <span class="keyword">if</span> (result.decision === <span class="string">'decline'</span>) {
      <span class="keyword">return</span> { approved: <span class="value">false</span>, reason: <span class="string">'high_risk'</span> };
    }

    <span class="keyword">if</span> (result.decision === <span class="string">'step_up'</span>) {
      <span class="keyword">return</span> <span class="keyword">await</span> <span class="method">trigger3DS</span>(txData);
    }

  } <span class="keyword">else</span> {
    <span class="comment">// Normal value — fire and forget post-auth</span>
    chargebackShield.transactions.<span class="method">score</span>(txData).<span class="method">catch</span>(console.error);
  }

  <span class="keyword">return await</span> cardNetwork.<span class="method">authorize</span>(txData);
}</pre>
</div>

<div class="callout callout-warning">
  <strong>Choosing your threshold.</strong>
  A good starting point is your 95th percentile transaction value.
  Most transactions go through the fast post-auth path.
  Only your largest transactions get the synchronous check.
</div>

{{-- Endpoint comparison --}}
<h2 id="endpoint-comparison">Endpoint comparison</h2>

<table class="param-table w-full border border-slate-200 rounded-xl overflow-hidden mb-6">
  <thead>
    <tr>
      <th>Feature</th>
      <th>POST /transaction/intercept</th>
      <th>POST /transaction/score</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td class="text-slate-600 text-sm font-medium">When to call</td>
      <td class="text-slate-600 text-sm">Before approving</td>
      <td class="text-slate-600 text-sm">After approving</td>
    </tr>
    <tr>
      <td class="text-slate-600 text-sm font-medium">Blocks payment?</td>
      <td class="text-slate-600 text-sm">Yes — you wait for decision</td>
      <td class="text-slate-600 text-sm">No — fire and forget</td>
    </tr>
    <tr>
      <td class="text-slate-600 text-sm font-medium">Returns decision?</td>
      <td class="text-slate-600 text-sm">Yes — allow / step_up / decline</td>
      <td class="text-slate-600 text-sm">Score only — no action needed</td>
    </tr>
    <tr>
      <td class="text-slate-600 text-sm font-medium">Locks evidence?</td>
      <td class="text-slate-600 text-sm">Yes (allow + step_up only)</td>
      <td class="text-slate-600 text-sm">Yes — always, every transaction</td>
    </tr>
    <tr>
      <td class="text-slate-600 text-sm font-medium">High-risk webhook?</td>
      <td class="text-slate-600 text-sm">transaction.declined</td>
      <td class="text-slate-600 text-sm">transaction.high_risk_detected</td>
    </tr>
    <tr>
      <td class="text-slate-600 text-sm font-medium">Latency added</td>
      <td class="text-slate-600 text-sm">~80ms</td>
      <td class="text-slate-600 text-sm">0ms</td>
    </tr>
    <tr>
      <td class="text-slate-600 text-sm font-medium">Best for</td>
      <td class="text-slate-600 text-sm">High-value, fraud-sensitive</td>
      <td class="text-slate-600 text-sm">All transactions at volume</td>
    </tr>
  </tbody>
</table>

<h3 id="idempotency">Idempotency</h3>
<p>
  Both endpoints require an <span class="inline-code">idempotency_key</span>.
  Use your internal transaction ID. If the same key is sent twice,
  the original response is returned without reprocessing.
  Keys are cached for 24 hours.
</p>

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
      <td><span class="inline-code">cs_live_</span></td>
    </tr>
  </tbody>
</table>
