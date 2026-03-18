<?php

namespace App\Enums;

enum ActorType: string
{
  case Merchant = 'merchant';
  case System   = 'system';

  public function label(): string
  {
    return match ($this) {
      self::Merchant => 'Merchant',
      self::System   => 'System',
    };
  }
}
