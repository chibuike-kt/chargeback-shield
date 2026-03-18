<?php

namespace Database\Factories;

use App\Enums\DisputeNetwork;
use App\Enums\DisputeStatus;
use App\Models\Dispute;
use App\Models\Merchant;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class DisputeFactory extends Factory
{
  protected $model = Dispute::class;

  private array $visaCodes = [
    '10.1' => 'EMV Liability Shift Counterfeit Fraud',
    '10.4' => 'Other Fraud – Card Absent Environment',
    '10.5' => 'Visa Fraud Monitoring Program',
    '11.1' => 'Card Recovery Bulletin',
    '12.5' => 'Incorrect Amount',
    '13.1' => 'Merchandise / Services Not Received',
    '13.3' => 'Not as Described or Defective Merchandise',
  ];

  private array $mastercardCodes = [
    '4853' => 'Cardholder Dispute',
    '4855' => 'Goods or Services Not Provided',
    '4859' => 'Services Not Rendered',
    '4863' => 'Cardholder Does Not Recognize',
  ];

  public function definition(): array
  {
    $network = $this->faker->randomElement(DisputeNetwork::cases());
    $codes   = $network === DisputeNetwork::Visa
      ? $this->visaCodes
      : $this->mastercardCodes;

    $code        = $this->faker->randomElement(array_keys($codes));
    $description = $codes[$code];
    $filedAt     = $this->faker->dateTimeBetween('-30 days', 'now');

    return [
      'transaction_id'     => Transaction::factory(),
      'merchant_id'        => Merchant::factory(),
      'reason_code'        => $code,
      'reason_description' => $description,
      'network'            => $network->value,
      'status'             => DisputeStatus::Open->value,
      'filed_at'           => $filedAt,
    ];
  }

  public function responded(): static
  {
    return $this->state(fn() => [
      'status'       => DisputeStatus::Responded->value,
      'responded_at' => now()->subHours(rand(1, 48)),
    ]);
  }

  public function won(): static
  {
    return $this->state(fn() => [
      'status'       => DisputeStatus::Won->value,
      'responded_at' => now()->subDays(rand(3, 10)),
      'resolved_at'  => now()->subDays(rand(1, 2)),
    ]);
  }

  public function lost(): static
  {
    return $this->state(fn() => [
      'status'       => DisputeStatus::Lost->value,
      'responded_at' => now()->subDays(rand(3, 10)),
      'resolved_at'  => now()->subDays(rand(1, 2)),
    ]);
  }
}
