<?php

namespace Database\Seeders;

use App\Actions\Disputes\GenerateDisputeResponse;
use App\Enums\DecisionType;
use App\Enums\DisputeStatus;
use App\Enums\TrustEventType;
use App\Models\Dispute;
use App\Models\Merchant;
use App\Models\MerchantTrustRegistry;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DisputeSeeder extends Seeder
{
  private array $reasonCodes = [
    ['code' => '4863', 'network' => 'mastercard'],
    ['code' => '4853', 'network' => 'mastercard'],
    ['code' => '10.4', 'network' => 'visa'],
    ['code' => '13.1', 'network' => 'visa'],
    ['code' => '4837', 'network' => 'mastercard'],
    ['code' => '10.1', 'network' => 'visa'],
  ];

  private array $statuses = [
    DisputeStatus::Responded,
    DisputeStatus::Won,
    DisputeStatus::Won,
    DisputeStatus::Lost,
    DisputeStatus::Open,
  ];

  public function run(): void
  {
    $generateResponse = app(GenerateDisputeResponse::class);

    $merchants = Merchant::all();

    foreach ($merchants as $merchant) {
      // Get approved transactions with evidence bundles
      $transactions = Transaction::where('merchant_id', $merchant->id)
        ->where('decision', DecisionType::Allow->value)
        ->whereHas('evidenceBundle')
        ->inRandomOrder()
        ->limit(8)
        ->get();

      foreach ($transactions as $index => $transaction) {
        $reasonCode = $this->reasonCodes[$index % count($this->reasonCodes)];
        $status     = $this->statuses[$index % count($this->statuses)];
        $filedAt    = Carbon::now()->subDays(rand(5, 25));

        $dispute = Dispute::create([
          'transaction_id'     => $transaction->id,
          'merchant_id'        => $merchant->id,
          'reason_code'        => $reasonCode['code'],
          'reason_description' => $this->getDescription($reasonCode['code']),
          'network'            => $reasonCode['network'],
          'status'             => DisputeStatus::Open->value,
          'filed_at'           => $filedAt,
          'created_at'         => $filedAt,
          'updated_at'         => $filedAt,
        ]);

        // Generate response document
        try {
          $responseDocument = $generateResponse->execute($dispute, $merchant);

          $dispute->update([
            'response_document' => $responseDocument,
            'status'            => $status->value,
            'responded_at'      => $filedAt->copy()->addHours(rand(1, 4)),
            'resolved_at'       => in_array($status, [DisputeStatus::Won, DisputeStatus::Lost])
              ? $filedAt->copy()->addDays(rand(7, 14))
              : null,
          ]);
        } catch (\Exception $e) {
          // Skip if response generation fails
          $dispute->update(['status' => $status->value]);
        }

        // Trust registry entry
        MerchantTrustRegistry::create([
          'merchant_id'    => $merchant->id,
          'transaction_id' => $transaction->id,
          'event_type'     => TrustEventType::ChargebackFiled->value,
          'penalty_points' => TrustEventType::ChargebackFiled->penaltyPoints(),
          'notes'          => "Seeded chargeback: {$reasonCode['code']}",
          'created_at'     => $filedAt,
        ]);

        if ($status === DisputeStatus::Won) {
          MerchantTrustRegistry::create([
            'merchant_id'    => $merchant->id,
            'transaction_id' => $transaction->id,
            'event_type'     => TrustEventType::DisputeWon->value,
            'penalty_points' => 0,
            'notes'          => "Dispute won: {$reasonCode['code']}",
            'created_at'     => $filedAt->copy()->addDays(rand(7, 14)),
          ]);
        }
      }
    }

    $this->command->info('✓ Disputes seeded');
  }

  private function getDescription(string $code): string
  {
    return match ($code) {
      '4863'  => 'Cardholder Does Not Recognize Transaction',
      '4853'  => 'Cardholder Dispute – Defective/Not as Described',
      '10.4'  => 'Other Fraud – Card Absent Environment',
      '13.1'  => 'Merchandise / Services Not Received',
      '4837'  => 'No Cardholder Authorization',
      '10.1'  => 'EMV Liability Shift Counterfeit Fraud',
      default => 'Unknown',
    };
  }
}
