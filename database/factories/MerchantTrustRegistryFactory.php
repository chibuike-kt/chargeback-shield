<?php

namespace Database\Factories;

use App\Enums\TrustEventType;
use App\Models\Merchant;
use App\Models\MerchantTrustRegistry;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class MerchantTrustRegistryFactory extends Factory
{
    protected $model = MerchantTrustRegistry::class;

    public function definition(): array
    {
        $eventType = $this->faker->randomElement(TrustEventType::cases());

        return [
            'merchant_id'    => Merchant::factory(),
            'transaction_id' => Transaction::factory(),
            'event_type'     => $eventType->value,
            'penalty_points' => $eventType->penaltyPoints(),
            'notes'          => $this->faker->optional()->sentence(),
            'created_at'     => $this->faker->dateTimeBetween('-60 days', 'now'),
        ];
    }
}
