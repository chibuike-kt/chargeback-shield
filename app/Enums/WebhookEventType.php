<?php

namespace App\Enums;

enum WebhookEventType: string
{
  case TransactionScored    = 'transaction.scored';
  case TransactionDeclined  = 'transaction.declined';
  case DisputeFiled         = 'dispute.filed';
  case DisputeResponded     = 'dispute.responded';
  case DisputeWon           = 'dispute.won';
  case DisputeLost          = 'dispute.lost';

  public function label(): string
  {
    return match ($this) {
      self::TransactionScored   => 'Transaction Scored',
      self::TransactionDeclined => 'Transaction Declined',
      self::DisputeFiled        => 'Dispute Filed',
      self::DisputeResponded    => 'Dispute Responded',
      self::DisputeWon          => 'Dispute Won',
      self::DisputeLost         => 'Dispute Lost',
    };
  }
}
