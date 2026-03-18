<?php

namespace Database\Factories;

use App\Enums\DecisionType;
use App\Enums\RiskLevel;
use App\Enums\TransactionStatus;
use App\Models\Merchant;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TransactionFactory extends Factory
{
  protected $model = Transaction::class;

  // Common African country codes
  private array $africanCountries = ['NG', 'GH', 'KE', 'ZA', 'TZ', 'UG', 'RW'];

  // Suspicious foreign countries for mismatch scenarios
  private array $foreignCountries = ['US', 'GB', 'CN', 'RU', 'BR'];

  public function definition(): array
  {
    $score = $this->faker->randomFloat(4, 0, 1);

    return [
      'merchant_id'        => Merchant::factory(),
      'idempotency_key'    => Str::uuid()->toString(),
      'card_bin'           => $this->faker->numerify('######'),
      'card_last4'         => $this->faker->numerify('####'),
      'card_country'       => $this->faker->randomElement($this->africanCountries),
      'amount'             => $this->faker->numberBetween(50000, 5000000), // kobo
      'currency'           => 'NGN',
      'ip_address'         => $this->faker->ipv4(),
      'ip_country'         => $this->faker->randomElement($this->africanCountries),
      'ip_city'            => $this->faker->city(),
      'device_fingerprint' => Str::random(32),
      'session_token'      => Str::random(32),
      'session_age_seconds' => $this->faker->numberBetween(60, 3600),
      'merchant_category'  => $this->faker->randomElement(['5411', '5812', '4111', '7372', '5999']),
      'risk_score'         => $score,
      'risk_level'         => RiskLevel::fromScore($score)->value,
      'decision'           => DecisionType::fromScore($score)->value,
      'status'             => TransactionStatus::Approved->value,
    ];
  }

  // ── Named states ──────────────────────────────────────────────────────────

  public function lowRisk(): static
  {
    return $this->state(function () {
      $score = $this->faker->randomFloat(4, 0.05, 0.35);
      return [
        'risk_score' => $score,
        'risk_level' => RiskLevel::Low->value,
        'decision'   => DecisionType::Allow->value,
        'status'     => TransactionStatus::Approved->value,
      ];
    });
  }

  public function mediumRisk(): static
  {
    return $this->state(function () {
      $score = $this->faker->randomFloat(4, 0.40, 0.69);
      return [
        'risk_score' => $score,
        'risk_level' => RiskLevel::Medium->value,
        'decision'   => DecisionType::StepUp->value,
        'status'     => TransactionStatus::Approved->value,
      ];
    });
  }

  public function highRisk(): static
  {
    return $this->state(function () {
      $score = $this->faker->randomFloat(4, 0.71, 0.99);
      return [
        'risk_score' => $score,
        'risk_level' => RiskLevel::High->value,
        'decision'   => DecisionType::Decline->value,
        'status'     => TransactionStatus::Declined->value,
      ];
    });
  }

  public function geoMismatch(): static
  {
    return $this->state(function () {
      return [
        'card_country' => $this->faker->randomElement($this->africanCountries),
        'ip_country'   => $this->faker->randomElement($this->foreignCountries),
      ];
    });
  }
}
