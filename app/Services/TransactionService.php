<?php

namespace App\Services;

use App\DTOs\ScoringResult;
use App\Enums\TransactionStatus;
use App\Models\Merchant;
use App\Models\RiskSignalLog;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class TransactionService
{
  /**
   * Create a transaction record and persist all risk signal logs.
   */
  public function createFromIntercept(
    Merchant $merchant,
    array $requestData,
    ScoringResult $scoringResult
  ): Transaction {
    return DB::transaction(function () use ($merchant, $requestData, $scoringResult) {

      // Determine final status from decision
      $status = match ($scoringResult->decision) {
        \App\Enums\DecisionType::Allow,
        \App\Enums\DecisionType::StepUp  => TransactionStatus::Approved,
        \App\Enums\DecisionType::Decline  => TransactionStatus::Declined,
      };

      $transaction = Transaction::create([
        'merchant_id'         => $merchant->id,
        'idempotency_key'     => $requestData['idempotency_key'],
        'card_bin'            => $requestData['card_bin'],
        'card_last4'          => $requestData['card_last4'],
        'card_country'        => $requestData['card_country'] ?? null,
        'amount'              => $requestData['amount'],
        'currency'            => strtoupper($requestData['currency']),
        'ip_address'          => $requestData['ip_address'] ?? null,
        'ip_country'          => $requestData['ip_country'] ?? null,
        'ip_city'             => $requestData['ip_city'] ?? null,
        'device_fingerprint'  => $requestData['device_fingerprint'] ?? null,
        'session_token'       => $requestData['session_token'] ?? null,
        'session_age_seconds' => $requestData['session_age_seconds'] ?? 0,
        'merchant_category'   => $requestData['merchant_category'] ?? null,
        'risk_score'          => $scoringResult->score,
        'risk_level'          => $scoringResult->riskLevel->value,
        'decision'            => $scoringResult->decision->value,
        'status'              => $status->value,
      ]);

      // Persist individual signal logs for full auditability
      $this->persistSignalLogs($transaction, $scoringResult->signals);

      return $transaction;
    });
  }

  /**
   * Create a transaction record for a post-authorization score.
   * The transaction is already approved by the fintech —
   * we record it as approved and score it for evidence purposes.
   */
  public function createPostAuth(
    Merchant $merchant,
    array $requestData,
    ScoringResult $scoringResult
  ): Transaction {
    return DB::transaction(function () use ($merchant, $requestData, $scoringResult) {

      $transaction = Transaction::create([
        'merchant_id'         => $merchant->id,
        'idempotency_key'     => $requestData['idempotency_key'],
        'card_bin'            => $requestData['card_bin'],
        'card_last4'          => $requestData['card_last4'],
        'card_country'        => $requestData['card_country'] ?? null,
        'amount'              => $requestData['amount'],
        'currency'            => strtoupper($requestData['currency']),
        'ip_address'          => $requestData['ip_address'] ?? null,
        'ip_country'          => $requestData['ip_country'] ?? null,
        'ip_city'             => $requestData['ip_city'] ?? null,
        'device_fingerprint'  => $requestData['device_fingerprint'] ?? null,
        'session_token'       => $requestData['session_token'] ?? null,
        'session_age_seconds' => $requestData['session_age_seconds'] ?? 0,
        'merchant_category'   => $requestData['merchant_category'] ?? null,
        'risk_score'          => $scoringResult->score,
        'risk_level'          => $scoringResult->riskLevel->value,
        // Decision reflects what our scorer would have decided
        // but status is always approved — fintech already processed it
        'decision'            => $scoringResult->decision->value,
        'status'              => TransactionStatus::Approved->value,
      ]);

      $this->persistSignalLogs($transaction, $scoringResult->signals);

      return $transaction;
    });
  }
  /**
   * Write each scoring signal as its own log row.
   */
  private function persistSignalLogs(Transaction $transaction, array $signals): void
  {
    $now  = now();
    $rows = [];

    foreach ($signals as $signal) {
      $rows[] = [
        'transaction_id'        => $transaction->id,
        'signal_name'           => $signal['signal_name'],
        'raw_value'             => (string) $signal['raw_value'],
        'normalized_score'      => $signal['normalized_score'],
        'weight'                => $signal['weight'],
        'weighted_contribution' => $signal['weighted_contribution'],
        'created_at'            => $now,
      ];
    }

    RiskSignalLog::insert($rows);
  }
}
