<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
  public function run(): void
  {
    $this->call([
      MerchantSeeder::class,
      TransactionSeeder::class,
      DisputeSeeder::class,
      WebhookDeliverySeeder::class,
      TrustRegistrySeeder::class,
      AuditLogSeeder::class,
    ]);
  }
}
