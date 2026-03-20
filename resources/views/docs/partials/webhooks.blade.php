<h2 id="webhooks">Webhooks</h2>

<p>
  Chargeback Shield fires a signed webhook to your configured endpoint
  for every significant event. Configure your webhook URL in the dashboard
  under Settings.
</p>

<h3>Event types</h3>

<table class="param-table w-full border border-slate-200 rounded-xl overflow-hidden mb-5">
  <thead>
    <tr>
      <th>Event</th>
      <th>Trigger</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><span class="inline-code">transaction.scored</span></td>
      <td class="text-slate-600 text-sm">Transaction approved or stepped up</td>
    </tr>
    <tr>
      <td><span class="inline-code">transaction.declined</span></td>
      <td class="text-slate-600 text-sm">Transaction declined by the scoring engine</td>
    </tr>
    <tr>
      <td><span class="inline-code">dispute.filed</span></td>
      <td class="text-slate-600 text-sm">Chargeback filed and response generated</td>
    </tr>
    <tr>
      <td><span class="inline-code">dispute.won</span></td>
      <td class="text-slate-600 text-sm">Dispute resolved as won</td>
    </tr>
    <tr>
      <td><span class="inline-code">dispute.lost</span></td>
      <td class="text-slate-600 text-sm">Dispute resolved as lost</td>
    </tr>
  </tbody>
</table>

<h3>Signature verification</h3>
<p>
  Every webhook payload is signed with your webhook secret.
  The signature is sent in the <span class="inline-code">X-Chargeback-Shield-Sig</span> header
  as <span class="inline-code">sha256=hex_signature</span>.
  Always verify this signature before processing the event.
</p>

<div class="code-block">
  <pre><span class="comment">// Node.js — verify webhook signature</span>
<span class="keyword">const</span> crypto = require(<span class="string">'crypto'</span>);

<span class="keyword">function</span> <span class="method">verifyWebhook</span>(payload, signature, secret) {
  <span class="keyword">const</span> expected = <span class="string">'sha256='</span> + crypto
    .createHmac(<span class="string">'sha256'</span>, secret)
    .update(JSON.stringify(payload))
    .digest(<span class="string">'hex'</span>);

  <span class="keyword">return</span> crypto.timingSafeEqual(
    Buffer.from(expected),
    Buffer.from(signature)
  );
}

<span class="comment">// In your webhook handler</span>
<span class="keyword">const</span> sig     = req.headers[<span class="string">'x-chargeback-shield-sig'</span>];
<span class="keyword">const</span> isValid = verifyWebhook(req.body, sig, process.env.WEBHOOK_SECRET);

<span class="keyword">if</span> (!isValid) <span class="keyword">return</span> res.status(<span class="value">401</span>).send(<span class="string">'Invalid signature'</span>);</pre>
</div>

<div class="code-block">
  <pre><span class="comment">// PHP — verify webhook signature</span>
<span class="keyword">function</span> <span class="method">verifyWebhook</span>(<span class="value">$payload</span>, <span class="value">$signature</span>, <span class="value">$secret</span>): bool {
    <span class="value">$expected</span> = <span class="string">'sha256='</span> . hash_hmac(
        <span class="string">'sha256'</span>,
        json_encode(<span class="value">$payload</span>),
        <span class="value">$secret</span>
    );

    <span class="keyword">return</span> hash_equals(<span class="value">$expected</span>, <span class="value">$signature</span>);
}

<span class="value">$sig</span>     = <span class="value">$_SERVER</span>[<span class="string">'HTTP_X_CHARGEBACK_SHIELD_SIG'</span>];
<span class="value">$isValid</span> = verifyWebhook(<span class="value">$payload</span>, <span class="value">$sig</span>, env(<span class="string">'WEBHOOK_SECRET'</span>));</pre>
</div>

<h3>Retry policy</h3>
<p>
  Failed webhook deliveries are retried up to 3 times with exponential backoff.
</p>

<table class="param-table w-full border border-slate-200 rounded-xl overflow-hidden mb-4">
  <thead>
    <tr>
      <th>Attempt</th>
      <th>Delay</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td class="text-slate-600 text-sm">Attempt 2</td>
      <td class="text-slate-600 text-sm">1 minute after failure</td>
    </tr>
    <tr>
      <td class="text-slate-600 text-sm">Attempt 3</td>
      <td class="text-slate-600 text-sm">5 minutes after second failure</td>
    </tr>
    <tr>
      <td class="text-slate-600 text-sm">Final attempt</td>
      <td class="text-slate-600 text-sm">15 minutes after third failure</td>
    </tr>
  </tbody>
</table>

<div class="callout callout-info">
  You can manually re-trigger any failed webhook from the Webhook Log in your dashboard.
</div>
