<h2 id="reason-codes">Reason code reference</h2>

<p>
  Chargeback Shield supports 15 reason codes across Visa and Mastercard.
  Each code has a tailored response strategy and winning argument built in.
</p>

<h3>Visa</h3>

<table class="param-table w-full border border-slate-200 rounded-xl overflow-hidden mb-6">
  <thead>
    <tr>
      <th>Code</th>
      <th>Description</th>
      <th>Category</th>
      <th>Time limit</th>
    </tr>
  </thead>
  <tbody>
    @php
    $visaCodes = [
    ['10.1', 'EMV Liability Shift Counterfeit Fraud', 'Fraud', '120 days'],
    ['10.4', 'Other Fraud – Card Absent Environment', 'Fraud', '120 days'],
    ['10.5', 'Visa Fraud Monitoring Program', 'Fraud', '120 days'],
    ['11.1', 'Card Recovery Bulletin', 'Authorization', '75 days'],
    ['12.5', 'Incorrect Transaction Amount', 'Processing Error', '120 days'],
    ['13.1', 'Merchandise / Services Not Received', 'Consumer Dispute', '120 days'],
    ['13.3', 'Not as Described or Defective', 'Consumer Dispute', '120 days'],
    ['13.6', 'Credit Not Processed', 'Consumer Dispute', '120 days'],
    ];
    @endphp
    @foreach($visaCodes as [$code, $desc, $cat, $limit])
    <tr>
      <td><span class="inline-code">{{ $code }}</span></td>
      <td class="text-slate-600 text-sm">{{ $desc }}</td>
      <td class="text-xs text-slate-400">{{ $cat }}</td>
      <td class="text-xs text-slate-400">{{ $limit }}</td>
    </tr>
    @endforeach
  </tbody>
</table>

<h3>Mastercard</h3>

<table class="param-table w-full border border-slate-200 rounded-xl overflow-hidden mb-6">
  <thead>
    <tr>
      <th>Code</th>
      <th>Description</th>
      <th>Category</th>
      <th>Time limit</th>
    </tr>
  </thead>
  <tbody>
    @php
    $mastercardCodes = [
    ['4853', 'Cardholder Dispute', 'Cardholder Dispute', '120 days'],
    ['4855', 'Goods or Services Not Provided', 'Cardholder Dispute', '120 days'],
    ['4859', 'Addendum, No-show, or ATM Dispute', 'Cardholder Dispute', '120 days'],
    ['4863', 'Cardholder Does Not Recognize', 'Fraud', '120 days'],
    ['4834', 'Duplicate Processing', 'Processing Error', '90 days'],
    ['4837', 'No Cardholder Authorization', 'Fraud', '120 days'],
    ['4840', 'Fraudulent Processing', 'Fraud', '120 days'],
    ];
    @endphp
    @foreach($mastercardCodes as [$code, $desc, $cat, $limit])
    <tr>
      <td><span class="inline-code">{{ $code }}</span></td>
      <td class="text-slate-600 text-sm">{{ $desc }}</td>
      <td class="text-xs text-slate-400">{{ $cat }}</td>
      <td class="text-xs text-slate-400">{{ $limit }}</td>
    </tr>
    @endforeach
  </tbody>
</table>

{{-- SDKs --}}
<h2 id="sdk-node">Node.js SDK</h2>

<p>Install the SDK:</p>

<div class="code-block">
  <pre><span class="keyword">npm</span> install chargeback-shield</pre>
</div>

<div class="code-block">
  <pre><span class="keyword">import</span> { ChargebackShield } <span class="keyword">from</span> <span class="string">'chargeback-shield'</span>;

<span class="keyword">const</span> shield = <span class="keyword">new</span> <span class="method">ChargebackShield</span>({
  apiKey: process.env.CHARGEBACK_SHIELD_API_KEY,
});

<span class="comment">// Intercept a transaction</span>
<span class="keyword">const</span> result = <span class="keyword">await</span> shield.transactions.<span class="method">intercept</span>({
  idempotencyKey:     <span class="string">'order_12345'</span>,
  cardBin:            <span class="string">'459234'</span>,
  cardLast4:          <span class="string">'4242'</span>,
  cardCountry:        <span class="string">'NG'</span>,
  amount:             <span class="value">500000</span>,
  currency:           <span class="string">'NGN'</span>,
  ipCountry:          <span class="string">'NG'</span>,
  deviceFingerprint:  <span class="string">'fp_abc123'</span>,
  sessionAgeSeconds:  <span class="value">900</span>,
});

<span class="keyword">if</span> (result.decision === <span class="string">'decline'</span>) {
  <span class="keyword">throw new</span> Error(<span class="string">'Transaction declined'</span>);
}

<span class="comment">// File a dispute</span>
<span class="keyword">const</span> dispute = <span class="keyword">await</span> shield.disputes.<span class="method">file</span>({
  transactionId: result.transactionId,
  reasonCode:    <span class="string">'4863'</span>,
  network:       <span class="string">'mastercard'</span>,
});</pre>
</div>

<h2 id="sdk-php">PHP / Laravel SDK</h2>

<p>Install via Composer:</p>

<div class="code-block">
  <pre><span class="keyword">composer</span> require atlastech/chargeback-shield</pre>
</div>

<div class="code-block">
  <pre><span class="keyword">use</span> AtlasTech\ChargebackShield\ChargebackShield;

<span class="value">$shield</span> = <span class="keyword">new</span> <span class="method">ChargebackShield</span>(config(<span class="string">'services.chargeback_shield.key'</span>));

<span class="comment">// Intercept a transaction</span>
<span class="value">$result</span> = <span class="value">$shield</span>->transactions-><span class="method">intercept</span>([
  <span class="string">'idempotency_key'</span>    => <span class="string">'order_12345'</span>,
  <span class="string">'card_bin'</span>           => <span class="string">'459234'</span>,
  <span class="string">'card_last4'</span>         => <span class="string">'4242'</span>,
  <span class="string">'card_country'</span>       => <span class="string">'NG'</span>,
  <span class="string">'amount'</span>             => <span class="value">500000</span>,
  <span class="string">'currency'</span>           => <span class="string">'NGN'</span>,
  <span class="string">'ip_country'</span>         => <span class="string">'NG'</span>,
  <span class="string">'device_fingerprint'</span> => <span class="string">'fp_abc123'</span>,
  <span class="string">'session_age_seconds'</span>=> <span class="value">900</span>,
]);

<span class="keyword">if</span> (<span class="value">$result</span>[<span class="string">'decision'</span>] === <span class="string">'decline'</span>) {
  <span class="keyword">throw new</span> \Exception(<span class="string">'Transaction declined'</span>);
}</pre>
</div>

<div class="callout callout-info">
  The SDKs are coming soon. In the meantime, use the REST API directly —
  all examples above show exactly what the SDK calls under the hood.
</div>
