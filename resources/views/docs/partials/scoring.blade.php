<h2 id="scoring">How scoring works</h2>

<p>
  Every transaction is scored by the Node.js scoring engine before a decision is returned.
  The engine computes 6 weighted signals and combines them into a single composite score
  between 0 and 1.
</p>

<h3 id="decisions">Signals and weights</h3>

<table class="param-table w-full border border-slate-200 rounded-xl overflow-hidden mb-5">
  <thead>
    <tr>
      <th>Signal</th>
      <th>Weight</th>
      <th>Description</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><span class="inline-code">velocity</span></td>
      <td class="text-slate-700 text-sm font-semibold">25%</td>
      <td class="text-slate-600 text-sm">Redis sliding windows: tx/hour, spend/24h, unique merchants/24h, unique countries/24h</td>
    </tr>
    <tr>
      <td><span class="inline-code">geo_mismatch</span></td>
      <td class="text-slate-700 text-sm font-semibold">20%</td>
      <td class="text-slate-600 text-sm">Card issuing country vs IP geolocation country. Intercontinental mismatch scores high.</td>
    </tr>
    <tr>
      <td><span class="inline-code">bin_risk</span></td>
      <td class="text-slate-700 text-sm font-semibold">20%</td>
      <td class="text-slate-600 text-sm">BIN lookup table. Known Nigerian bank BINs score low. Prepaid and virtual card ranges score high.</td>
    </tr>
    <tr>
      <td><span class="inline-code">device_fingerprint</span></td>
      <td class="text-slate-700 text-sm font-semibold">15%</td>
      <td class="text-slate-600 text-sm">Missing or suspicious fingerprints carry elevated risk.</td>
    </tr>
    <tr>
      <td><span class="inline-code">session_age</span></td>
      <td class="text-slate-700 text-sm font-semibold">10%</td>
      <td class="text-slate-600 text-sm">Brand new sessions (0–60s) are a strong fraud indicator.</td>
    </tr>
    <tr>
      <td><span class="inline-code">amount_risk</span></td>
      <td class="text-slate-700 text-sm font-semibold">10%</td>
      <td class="text-slate-600 text-sm">Very small amounts (card testing) and very large amounts both carry elevated risk.</td>
    </tr>
  </tbody>
</table>

<div class="callout callout-info">
  <strong>Composite score</strong> = sum of (normalized_signal_score × weight).
  Every signal is individually logged so you can see exactly why a transaction
  scored the way it did.
</div>

<h3>Velocity windows</h3>
<p>
  Velocity is computed using Redis sorted sets as exact sliding windows.
  Each transaction is stored as a member scored by its Unix timestamp.
  To count events in the last hour, stale entries are removed and the
  remaining members are counted. No drift, no approximation.
</p>

<p>The four velocity dimensions tracked per card:</p>
<ul>
  <li>Transactions per hour</li>
  <li>Total spend in the last 24 hours (in major currency units)</li>
  <li>Unique merchant categories in the last 24 hours</li>
  <li>Unique countries in the last 24 hours</li>
</ul>
