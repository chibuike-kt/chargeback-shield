<section class="py-20 px-6 border-t border-b border-slate-100">
  <div class="max-w-6xl mx-auto">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 text-center">
      @php
      $stats = [
      ['value' => '< 100ms', 'label'=> 'Transaction scoring time'],
        ['value' => '15', 'label' => 'Visa + Mastercard reason codes'],
        ['value' => '6', 'label' => 'Real-time risk signals'],
        ['value' => '1', 'label' => 'API endpoint to integrate'],
        ];
        @endphp
        @foreach($stats as $stat)
        <div>
          <p class="text-4xl font-black gradient-text mb-2">{{ $stat['value'] }}</p>
          <p class="text-sm text-slate-500">{{ $stat['label'] }}</p>
        </div>
        @endforeach
    </div>
  </div>
</section>
