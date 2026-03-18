<?php

namespace Database\Seeders;

use App\Enums\TrustEventType;
use App\Models\Merchant;
use App\Models\MerchantTrustRegistry;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TrustRegistrySeeder extends Seeder
{
  public function run(): void
  {
    $merchants = Merchant::all();

    foreach ($merchants as $merchant) {
      $tx = Transaction::where('merchant_id', $merchant->id)
        ->inRandomOrder()
        ->first();

      if (!$tx) continue;

      // Add a suspicious pattern entry
      MerchantTrustRegistry::create([
        'merchant_id'    => $merchant->id,
        'transaction_id' => $tx->id,
        'event_type'     => TrustEventType::SuspiciousPattern->value,
        'penalty_points' => TrustEventType::SuspiciousPattern->penaltyPoints(),
        'notes'          => 'Elevated velocity detected across multiple sessions',
        'created_at'     => Carbon::now()->subDays(rand(10, 20)),
      ]);
    }

    $this->command->info('✓ Trust registry seeded');
  }
}
