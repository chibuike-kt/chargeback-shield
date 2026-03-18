<?php

namespace App\Enums;

enum WebhookStatus: string
{
  case Pending   = 'pending';
  case Delivered = 'delivered';
  case Failed    = 'failed';
  case Retrying  = 'retrying';

  public function label(): string
  {
    return match ($this) {
      self::Pending   => 'Pending',
      self::Delivered => 'Delivered',
      self::Failed    => 'Failed',
      self::Retrying  => 'Retrying',
    };
  }

  public function badgeClass(): string
  {
    return match ($this) {
      self::Pending   => 'badge-slate',
      self::Delivered => 'badge-green',
      self::Failed    => 'badge-red',
      self::Retrying  => 'badge-yellow',
    };
  }
}
