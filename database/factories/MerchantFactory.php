<?php

namespace Database\Factories;

use App\Actions\Merchants\GenerateMerchantCredentials;
use App\Models\Merchant;
use Illuminate\Database\Eloquent\Factories\Factory;

class MerchantFactory extends Factory
{
  protected $model = Merchant::class;

  public function definition(): array
  {
    $credentials = new GenerateMerchantCredentials();

    return [
      'company_name'   => $this->faker->company(),
      'email'          => $this->faker->unique()->safeEmail(),
      'password'       => bcrypt('password'),
      'api_key'        => $credentials->generateApiKey(),
      'webhook_secret' => $credentials->generateWebhookSecret(),
      'webhook_url'    => 'https://webhook.site/' . $this->faker->uuid(),
      'is_active'      => true,
    ];
  }
}
