<h2 id="evidence-vault">Evidence vault</h2>

<p>
  Every approved or stepped-up transaction produces an evidence bundle.
  This bundle is the cryptographic proof that the transaction was legitimate
  at the time of authorization.
</p>

<h3>What the bundle contains</h3>
<ul>
  <li>Full transaction context — amount, currency, card details, timestamp</li>
  <li>Network signals — IP address, IP country, IP city</li>
  <li>Device signals — fingerprint, session token, session age</li>
  <li>Risk assessment — composite score, risk level, all 6 signal breakdowns</li>
  <li>Merchant context — merchant ID, company name, category code</li>
  <li>Lock timestamp — exact moment the bundle was created</li>
</ul>

<h3>Encryption and signing</h3>
<p>
  The bundle payload is <strong>AES-256-CBC encrypted</strong> with a random IV
  before storage. It is <strong>HMAC-SHA256 signed</strong> using the merchant's
  webhook secret as the signing key. The signing key is derived via SHA-256
  to ensure it is always the correct length for AES-256.
</p>

<h3>Immutability</h3>
<p>
  Evidence bundles are write-once. The
  <span class="inline-code">EvidenceBundle</span> model throws a
  <span class="inline-code">RuntimeException</span> if any code attempts
  to update an existing record. The database table has no
  <span class="inline-code">updated_at</span> column. There is no update path.
</p>

<div class="callout callout-success">
  The bundle is locked <strong>before</strong> a chargeback exists.
  When a cardholder claims they didn't make the transaction,
  the proof was created at the exact moment the transaction was approved —
  not after the dispute was filed.
</div>
