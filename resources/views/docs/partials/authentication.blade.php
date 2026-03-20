<h2 id="authentication">Authentication</h2>

<p>
  Every API request must include your merchant API key in the
  <span class="inline-code">X-API-Key</span> header.
  Keys are generated automatically when you register and are available
  in your dashboard.
</p>

<div class="code-block">
  <pre><span class="key">X-API-Key</span>: <span class="string">cs_live_a1b2c3d4e5f6...</span></pre>
</div>

<p>
  You can also pass the key as a Bearer token in the
  <span class="inline-code">Authorization</span> header:
</p>

<div class="code-block">
  <pre><span class="key">Authorization</span>: <span class="string">Bearer cs_live_a1b2c3d4e5f6...</span></pre>
</div>

<div class="callout callout-warning">
  Never expose your API key in client-side code or public repositories.
  All API calls should be made from your backend server.
</div>

<h3>Error responses</h3>

<table class="param-table w-full border border-slate-200 rounded-xl overflow-hidden mb-4">
  <thead>
    <tr>
      <th>Status</th>
      <th>Meaning</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><span class="inline-code">401</span></td>
      <td class="text-slate-600">Missing or invalid API key</td>
    </tr>
    <tr>
      <td><span class="inline-code">422</span></td>
      <td class="text-slate-600">Validation failed — check the errors object</td>
    </tr>
    <tr>
      <td><span class="inline-code">404</span></td>
      <td class="text-slate-600">Resource not found or belongs to another merchant</td>
    </tr>
    <tr>
      <td><span class="inline-code">409</span></td>
      <td class="text-slate-600">Conflict — e.g. duplicate dispute for same transaction</td>
    </tr>
    <tr>
      <td><span class="inline-code">500</span></td>
      <td class="text-slate-600">Internal server error</td>
    </tr>
  </tbody>
</table>
