<?php

namespace App\Enums;

enum TrustEventType: string
{
  case FraudConfirmed    = 'fraud_confirmed';
  case ChargebackFiled   = 'chargeback_filed';
  case DisputeWon        = 'dispute_won';
  case DisputeLost       = 'dispute_lost';
  case SuspiciousPattern = 'suspicious_pattern';

  public function label(): string
  {
    return match ($this) {
      self::FraudConfirmed    => 'Fraud Confirmed',
      self::ChargebackFiled   => 'Chargeback Filed',
      self::DisputeWon        => 'Dispute Won',
      self::DisputeLost       => 'Dispute Lost',
      self::SuspiciousPattern => 'Suspicious Pattern',
    };
  }

  public function penaltyPoints(): int
  {
    return match ($this) {
      self::FraudConfirmed    => 50,
      self::ChargebackFiled   => 20,
      self::DisputeLost       => 10,
      self::SuspiciousPattern => 15,
      self::DisputeWon        => 0,
    };
  }
}
