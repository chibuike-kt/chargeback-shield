@extends('layouts.landing')

@section('content')
@include('landing.partials.nav')
@include('landing.partials.hero')
@include('landing.partials.stats')
@include('landing.partials.how-it-works')
@include('landing.partials.features')
@include('landing.partials.signals')
@include('landing.partials.pricing')
@include('landing.partials.cta')
@include('landing.partials.footer')
@endsection

@push('scripts')
<script>
  function heroFeed() {
    return {
      events: [],
      maxEvents: 6,
      timer: null,

      init() {
        var self = this;

        var mockEvents = [{
            last4: '4242',
            bin: '459234',
            amount: 'NGN 5,000.00',
            route: 'NG → NG',
            decision: 'allow',
            score: 0.124,
            label: 'Approved'
          },
          {
            last4: '1111',
            bin: '520000',
            amount: 'NGN 35,000.00',
            route: 'GH → NG',
            decision: 'step_up',
            score: 0.512,
            label: 'Step-Up'
          },
          {
            last4: '9999',
            bin: '670123',
            amount: 'NGN 150,000.00',
            route: 'RU → NG',
            decision: 'decline',
            score: 0.891,
            label: 'Declined'
          },
          {
            last4: '5678',
            bin: '440647',
            amount: 'NGN 12,500.00',
            route: 'NG → NG',
            decision: 'allow',
            score: 0.087,
            label: 'Approved'
          },
          {
            last4: '0001',
            bin: '490123',
            amount: 'NGN 100.00',
            route: 'NG → NG',
            decision: 'decline',
            score: 0.923,
            label: 'Declined'
          },
          {
            last4: '3456',
            bin: '539983',
            amount: 'NGN 8,750.00',
            route: 'KE → KE',
            decision: 'allow',
            score: 0.156,
            label: 'Approved'
          },
        ];

        var index = 0;

        function addEvent() {
          var event = Object.assign({}, mockEvents[index % mockEvents.length], {
            id: Date.now() + Math.random()
          });

          self.events.unshift(event);

          if (self.events.length > self.maxEvents) {
            self.events = self.events.slice(0, self.maxEvents);
          }

          index++;
        }

        // Seed initial events
        setTimeout(function() {
          addEvent();
        }, 300);
        setTimeout(function() {
          addEvent();
        }, 800);
        setTimeout(function() {
          addEvent();
        }, 1400);

        // Then add one every 2.5 seconds
        self.timer = setInterval(function() {
          addEvent();
        }, 2500);
      },

      decisionColor: function(d) {
        return {
          allow: '#059669',
          step_up: '#d97706',
          decline: '#dc2626'
        } [d] || '#64748b';
      },
      decisionBg: function(d) {
        return {
          allow: '#ecfdf5',
          step_up: '#fffbeb',
          decline: '#fef2f2'
        } [d] || '#f1f5f9';
      },
      scoreColor: function(s) {
        return s < 0.4 ? '#059669' : s < 0.7 ? '#d97706' : '#dc2626';
      },
    };
  }
</script>
@endpush
