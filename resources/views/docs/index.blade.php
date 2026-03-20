@extends('layouts.docs')
@section('title', 'Documentation')

@section('on-page-nav')
<div class="space-y-1.5 text-xs">
  <a href="#quickstart" class="block text-slate-500 hover:text-indigo-600 py-0.5">Quickstart</a>
  <a href="#authentication" class="block text-slate-500 hover:text-indigo-600 py-0.5">Authentication</a>
  <a href="#idempotency" class="block text-slate-500 hover:text-indigo-600 py-0.5">Idempotency</a>
  <a href="#intercept" class="block text-slate-500 hover:text-indigo-600 py-0.5">Intercept transaction</a>
  <a href="#get-evidence" class="block text-slate-500 hover:text-indigo-600 py-0.5">Get evidence</a>
  <a href="#file-dispute" class="block text-slate-500 hover:text-indigo-600 py-0.5">File dispute</a>
  <a href="#scoring" class="block text-slate-500 hover:text-indigo-600 py-0.5">Scoring engine</a>
  <a href="#evidence-vault" class="block text-slate-500 hover:text-indigo-600 py-0.5">Evidence vault</a>
  <a href="#webhooks" class="block text-slate-500 hover:text-indigo-600 py-0.5">Webhooks</a>
  <a href="#reason-codes" class="block text-slate-500 hover:text-indigo-600 py-0.5">Reason codes</a>
  <a href="#sdk-node" class="block text-slate-500 hover:text-indigo-600 py-0.5">Node.js SDK</a>
  <a href="#sdk-php" class="block text-slate-500 hover:text-indigo-600 py-0.5">PHP SDK</a>
</div>
@endsection

@section('content')

{{-- Intro --}}
<div class="mb-10">
  <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full text-xs font-semibold mb-4"
    style="background:#eef2ff;color:#6366f1;">
    v1.0
  </div>
  <h1 class="text-3xl font-black text-slate-900 mb-3">Chargeback Shield API</h1>
  <p class="text-lg text-slate-500 leading-relaxed">
    Real-time chargeback protection for African fintechs. One API endpoint.
    15 minutes to your first scored transaction.
  </p>
</div>

{{-- Base URL --}}
<div class="code-block mb-8">
  <pre><span class="comment"># Base URL</span>
<span class="string">https://api.chargebackshield.io/api/v1</span>

<span class="comment"># All requests require:</span>
<span class="key">X-API-Key</span>: <span class="value">cs_live_your_key_here</span>
<span class="key">Content-Type</span>: <span class="value">application/json</span></pre>
</div>

@include('docs.partials.quickstart')
@include('docs.partials.authentication')
@include('docs.partials.api-reference')
@include('docs.partials.scoring')
@include('docs.partials.evidence')
@include('docs.partials.webhooks')
@include('docs.partials.reason-codes')

@endsection
