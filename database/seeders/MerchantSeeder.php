<?php

namespace Database\Seeders;

use App\Actions\Merchants\GenerateMerchantCredentials;
use App\Models\Merchant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MerchantSeeder extends Seeder
{
  public function run(): void
  {
    $credentials = new GenerateMerchantCredentials();

    $merchants = [
      [
        'company_name'   => 'Flutterwave Demo',
        'email'          => 'demo@flutterwave.test',
        'password'       => Hash::make('password'),
        'api_key'        => 'cs_live_demo_flutterwave_' . bin2hex(random_bytes(12)),
        'webhook_secret' => 'whsec_demo_' . bin2hex(random_bytes(12)),
        'webhook_url'    => 'https://webhook.site/demo-flutterwave',
        'is_active'      => true,
      ],
      [
        'company_name'   => 'Paystack Demo',
        'email'          => 'demo@paystack.test',
        'password'       => Hash::make('password'),
        'api_key'        => 'cs_live_demo_paystack_' . bin2hex(random_bytes(12)),
        'webhook_secret' => 'whsec_demo_' . bin2hex(random_bytes(12)),
        'webhook_url'    => 'https://webhook.site/demo-paystack',
        'is_active'      => true,
      ],
    ];

    foreach ($merchants as $data) {
      Merchant::firstOrCreate(
        ['email' => $data['email']],
        $data
      );
    }

    $this->command->info('✓ Demo merchants seeded');
  }
}
