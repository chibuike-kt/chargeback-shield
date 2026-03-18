<?php

namespace App\Enums;

enum TransactionStatus: string
{
  case Pending  = 'pending';
  case Approved = 'approved';
  case Declined = 'declined';

  public function label(): string
  {
    return match ($this) {
      self::Pending  => 'Pending',
      self::Approved => 'Approved',
      self::Declined => 'Declined',
    };
  }

  public function badgeClass(): string
  {
    return match ($this) {
      self::Pending  => 'badge-yellow',
      self::Approved => 'badge-green',
      self::Declined => 'badge-red',
    };
  }
}
