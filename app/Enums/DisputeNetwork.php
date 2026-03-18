<?php

namespace App\Enums;

enum DisputeNetwork: string
{
  case Visa       = 'visa';
  case Mastercard = 'mastercard';

  public function label(): string
  {
    return match ($this) {
      self::Visa       => 'Visa',
      self::Mastercard => 'Mastercard',
    };
  }
}
