<section id="pricing" class="py-24 px-6"
  style="background: linear-gradient(180deg, #f1f5f9 0%, #fafafa 100%);">
  <div class="max-w-6xl mx-auto">

    <div class="text-center mb-16">
      <span class="text-xs font-bold uppercase tracking-widest text-indigo-500 mb-4 block">
        Pricing
      </span>
      <h2 class="text-4xl font-black text-slate-900 mb-4">
        Simple. Volume-based. No surprises.
      </h2>
      <p class="text-lg text-slate-500">
        Start free. Pay as you scale.
      </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-5xl mx-auto">
      @php
      $plans = [
      [
      'name' => 'Starter',
      'price' => 'Free',
      'sub' => 'Forever',
      'limit' => 'Up to 1,000 transactions/mo',
      'features' => [
      'Full risk scoring (6 signals)',
      'Evidence vault',
      'Dispute response generation',
      'Webhook delivery',
      'Dashboard access',
      ],
      'cta' => 'Start for Free',
      'popular' => false,
      'href' => '/app/register',
      ],
      [
      'name' => 'Growth',
      'price' => '$49',
      'sub' => 'per month',
      'limit' => 'Up to 50,000 transactions/mo',
      'features' => [
      'Everything in Starter',
      'Priority webhook delivery',
      'PDF dispute response export',
      'Advanced velocity tuning',
      'Email support',
      ],
      'cta' => 'Start for Free',
      'popular' => true,
      'href' => '/app/register',
      ],
      [
      'name' => 'Scale',
      'price' => 'Custom',
      'sub' => 'contact us',
      'limit' => 'Unlimited transactions',
      'features' => [
      'Everything in Growth',
      'Dedicated infrastructure',
      'Custom reason code mapping',
      'SLA guarantee',
      'Dedicated support',
      ],
      'cta' => 'Contact us',
      'popular' => false,
      'href' => 'mailto:hello@chargebackshield.io',
      ],
      ];
      @endphp

      @foreach($plans as $plan)
      <div class="relative bg-white rounded-2xl p-7 border transition-all
                {{ $plan['popular'] ? 'border-indigo-400 shadow-lg shadow-indigo-100' : 'border-slate-200' }}">

        @if($plan['popular'])
        <div class="absolute -top-3 left-1/2 -translate-x-1/2">
          <span class="text-xs font-bold px-3 py-1 rounded-full text-white"
            style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
            Most popular
          </span>
        </div>
        @endif

        <div class="mb-6">
          <p class="text-sm font-bold text-slate-500 mb-2">{{ $plan['name'] }}</p>
          <div class="flex items-baseline gap-1 mb-1">
            <span class="text-4xl font-black text-slate-900">{{ $plan['price'] }}</span>
            <span class="text-sm text-slate-400">{{ $plan['sub'] }}</span>
          </div>
          <p class="text-xs text-slate-400">{{ $plan['limit'] }}</p>
        </div>

        <ul class="space-y-3 mb-7">
          @foreach($plan['features'] as $feature)
          <li class="flex items-center gap-2.5 text-sm text-slate-600">
            <svg class="w-4 h-4 shrink-0 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            {{ $feature }}
          </li>
          @endforeach
        </ul>

        <a href="{{ $plan['href'] }}"
          class="block w-full text-center py-3 rounded-xl text-sm font-bold transition-all
                    {{ $plan['popular']
                        ? 'text-white hover:opacity-90'
                        : 'text-slate-700 border border-slate-200 hover:border-slate-300 hover:bg-slate-50' }}"
          @if($plan['popular']) style="background: linear-gradient(135deg, #6366f1, #8b5cf6);" @endif>
          {{ $plan['cta'] }}
        </a>
      </div>
      @endforeach
    </div>
  </div>
</section>
