<?php

namespace Database\Factories;

use App\Enums\WebhookEventType;
use App\Enums\WebhookStatus;
use App\Models\Merchant;
use App\Models\Transaction;
use App\Models\WebhookDelivery;
use Illuminate\Database\Eloquent\Factories\Factory;

class WebhookDeliveryFactory extends Factory
{
  protected $model = WebhookDelivery::class;

  public function definition(): array
  {
    return [
      'merchant_id'    => Merchant::factory(),
      'transaction_id' => Transaction::factory(),
      'event_type'     => $this->faker->randomElement(WebhookEventType::cases())->value,
      'payload'        => ['event' => 'transaction.scored', 'data' => ['id' => $this->faker->uuid()]],
      'url'            => 'https://webhook.site/' . $this->faker->uuid(),
      'http_status'    => 200,
      'response_body'  => '{"received":true}',
      'attempt_number' => 1,
      'status'         => WebhookStatus::Delivered->value,
      'next_retry_at'  => null,
    ];
  }

  public function failed(): static
  {
    return $this->state(fn() => [
      'http_status'   => $this->faker->randomElement([500, 502, 503, null]),
      'response_body' => 'Connection refused',
      'attempt_number' => 3,
      'status'        => WebhookStatus::Failed->value,
      'next_retry_at' => null,
    ]);
  }

  public function retrying(): static
  {
    return $this->state(fn() => [
      'http_status'   => 503,
      'response_body' => 'Service Unavailable',
      'attempt_number' => $this->faker->numberBetween(1, 2),
      'status'        => WebhookStatus::Retrying->value,
      'next_retry_at' => now()->addMinutes(rand(2, 30)),
    ]);
  }
}
