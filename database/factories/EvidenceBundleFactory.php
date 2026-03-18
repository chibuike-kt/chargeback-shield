<?php

namespace Database\Factories;

use App\Models\EvidenceBundle;
use App\Models\Merchant;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class EvidenceBundleFactory extends Factory
{
  protected $model = EvidenceBundle::class;

  public function definition(): array
  {
    return [
      'transaction_id'    => Transaction::factory(),
      'merchant_id'       => Merchant::factory(),
      'payload_encrypted' => base64_encode($this->faker->text(200)),
      'encryption_iv'     => base64_encode(random_bytes(16)),
      'hmac_signature'    => hash('sha256', $this->faker->uuid()),
      'is_verified'       => true,
      'created_at'        => now(),
    ];
  }
}
