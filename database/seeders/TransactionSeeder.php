<?php

namespace Database\Seeders;

use App\Actions\Transactions\CreateEvidenceBundle;
use App\Enums\DecisionType;
use App\Enums\RiskLevel;
use App\Enums\TransactionStatus;
use App\Models\Merchant;
use App\Models\RiskSignalLog;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TransactionSeeder extends Seeder
{
  private array $africanCards = [
    ['bin' => '459234', 'last4' => '4242', 'country' => 'NG'],
    ['bin' => '507822', 'last4' => '1234', 'country' => 'NG'],
    ['bin' => '650002', 'last4' => '5678', 'country' => 'GH'],
    ['bin' => '440647', 'last4' => '9012', 'country' => 'NG'],
    ['bin' => '539983', 'last4' => '3456', 'country' => 'KE'],
    ['bin' => '520000', 'last4' => '7890', 'country' => 'ZA'],
  ];

  private array $highRiskCards = [
    ['bin' => '670123', 'last4' => '0001', 'country' => 'NG'],
    ['bin' => '490123', 'last4' => '0002', 'country' => 'NG'],
    ['bin' => '556789', 'last4' => '0003', 'country' => 'NG'],
  ];

  private array $merchantCategories = [
    '5411',
    '5812',
    '4111',
    '7372',
    '5999',
    '5734',
    '5045',
  ];

  private array $nigeriaCities = [
    'Lagos',
    'Abuja',
    'Port Harcourt',
    'Kano',
    'Ibadan',
  ];

  public function run(): void
  {
    $merchants = Merchant::all();

    if ($merchants->isEmpty()) {
      $this->command->warn('No merchants found — run MerchantSeeder first');
      return;
    }

    $createBundle = app(CreateEvidenceBundle::class);

    foreach ($merchants as $merchant) {
      $this->seedTransactionsForMerchant($merchant, $createBundle);
    }

    $this->command->info('✓ Transactions seeded');
  }

  private function seedTransactionsForMerchant(
    Merchant $merchant,
    CreateEvidenceBundle $createBundle
  ): void {
    // Create 60 transactions spread over last 30 days
    $scenarios = [
      // 35 low risk approved
      ...array_fill(0, 35, 'low_risk'),
      // 10 medium risk step-up
      ...array_fill(0, 10, 'medium_risk'),
      // 10 high risk declined
      ...array_fill(0, 10, 'high_risk'),
      // 5 card testing (rapid micro-transactions)
      ...array_fill(0, 5, 'card_testing'),
    ];

    shuffle($scenarios);

    foreach ($scenarios as $index => $scenario) {
      $daysAgo  = rand(0, 30);
      $hoursAgo = rand(0, 23);
      $createdAt = Carbon::now()
        ->subDays($daysAgo)
        ->subHours($hoursAgo)
        ->subMinutes(rand(0, 59));

      $tx = $this->createTransaction(
        $merchant,
        $scenario,
        $createdAt
      );

      // Create evidence bundles for approved/stepped-up
      if ($tx->decision !== DecisionType::Decline) {
        try {
          $bundle = $createBundle->execute($tx, $merchant);
        } catch (\Exception $e) {
          // Skip if bundle creation fails for seeded data
        }
      }
    }
  }

  private function createTransaction(
    Merchant $merchant,
    string $scenario,
    Carbon $createdAt
  ): Transaction {
    switch ($scenario) {
      case 'low_risk':
        $card       = $this->africanCards[array_rand($this->africanCards)];
        $score      = round(rand(5, 35) / 100, 4);
        $decision   = DecisionType::Allow;
        $status     = TransactionStatus::Approved;
        $amount     = rand(50000, 2000000);
        $ipCountry  = $card['country'];
        $device     = 'fp_known_' . Str::random(12);
        $sessionAge = rand(600, 7200);
        break;

      case 'medium_risk':
        $card       = $this->africanCards[array_rand($this->africanCards)];
        $score      = round(rand(40, 68) / 100, 4);
        $decision   = DecisionType::StepUp;
        $status     = TransactionStatus::Approved;
        $amount     = rand(2000000, 10000000);
        $ipCountry  = ['GH', 'KE', 'ZA'][rand(0, 2)];
        $device     = 'fp_semi_' . Str::random(12);
        $sessionAge = rand(120, 600);
        break;

      case 'high_risk':
        $card       = $this->highRiskCards[array_rand($this->highRiskCards)];
        $score      = round(rand(71, 97) / 100, 4);
        $decision   = DecisionType::Decline;
        $status     = TransactionStatus::Declined;
        $amount     = rand(5000000, 25000000);
        $ipCountry  = ['RU', 'CN', 'BR'][rand(0, 2)];
        $device     = null;
        $sessionAge = rand(0, 30);
        break;

      case 'card_testing':
        $card       = $this->highRiskCards[0];
        $score      = round(rand(75, 95) / 100, 4);
        $decision   = DecisionType::Decline;
        $status     = TransactionStatus::Declined;
        $amount     = rand(5000, 15000);
        $ipCountry  = 'NG';
        $device     = 'fp_attacker_' . Str::random(8);
        $sessionAge = rand(5, 30);
        break;

      default:
        $card       = $this->africanCards[0];
        $score      = 0.1;
        $decision   = DecisionType::Allow;
        $status     = TransactionStatus::Approved;
        $amount     = 100000;
        $ipCountry  = 'NG';
        $device     = 'fp_default';
        $sessionAge = 900;
    }

    $tx = Transaction::create([
      'merchant_id'         => $merchant->id,
      'idempotency_key'     => 'seed-' . Str::uuid(),
      'card_bin'            => $card['bin'],
      'card_last4'          => $card['last4'],
      'card_country'        => $card['country'],
      'amount'              => $amount,
      'currency'            => 'NGN',
      'ip_address'          => $this->randomIp(),
      'ip_country'          => $ipCountry,
      'ip_city'             => $this->nigeriaCities[array_rand($this->nigeriaCities)],
      'device_fingerprint'  => $device,
      'session_token'       => 'sess_' . Str::random(16),
      'session_age_seconds' => $sessionAge,
      'merchant_category'   => $this->merchantCategories[array_rand($this->merchantCategories)],
      'risk_score'          => $score,
      'risk_level'          => RiskLevel::fromScore($score)->value,
      'decision'            => $decision->value,
      'status'              => $status->value,
      'created_at'          => $createdAt,
      'updated_at'          => $createdAt,
    ]);

    // Seed signal logs
    $this->seedSignalLogs($tx, $score, $createdAt);

    return $tx;
  }

  private function seedSignalLogs(
    Transaction $tx,
    float $score,
    Carbon $createdAt
  ): void {
    $signals = [
      ['velocity',          0.25],
      ['geo_mismatch',      0.20],
      ['bin_risk',          0.20],
      ['device_fingerprint', 0.15],
      ['session_age',       0.10],
      ['amount_risk',       0.10],
    ];

    $rows = [];
    foreach ($signals as [$name, $weight]) {
      $normalizedScore = min(1.0, max(0.0, $score + rand(-15, 15) / 100));
      $rows[] = [
        'transaction_id'        => $tx->id,
        'signal_name'           => $name,
        'raw_value'             => $this->fakeRawValue($name),
        'normalized_score'      => round($normalizedScore, 4),
        'weight'                => $weight,
        'weighted_contribution' => round($normalizedScore * $weight, 4),
        'created_at'            => $createdAt,
      ];
    }

    RiskSignalLog::insert($rows);
  }

  private function fakeRawValue(string $signal): string
  {
    return match ($signal) {
      'velocity'           => 'tx_hour:' . rand(1, 5) . ' spend_24h:' . rand(1000, 50000),
      'geo_mismatch'       => ['NG==NG (match)', 'NG->GH (africa_cross_border)', 'NG->RU (high_risk)'][rand(0, 2)],
      'bin_risk'           => '459234 (known_bin)',
      'device_fingerprint' => 'fp_known_abc123 (valid_fingerprint)',
      'session_age'        => rand(60, 3600) . 's (established_session)',
      'amount_risk'        => 'NGN ' . number_format(rand(500, 50000), 2) . ' (normal_amount)',
      default              => 'unknown',
    };
  }

  private function randomIp(): string
  {
    return rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255);
  }
}
