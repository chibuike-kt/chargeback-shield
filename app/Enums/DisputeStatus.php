<?php

namespace App\Enums;

enum DisputeStatus: string
{
  case Open       = 'open';
  case Responded  = 'responded';
  case Won        = 'won';
  case Lost       = 'lost';

  public function label(): string
  {
    return match ($this) {
      self::Open      => 'Open',
      self::Responded => 'Responded',
      self::Won       => 'Won',
      self::Lost      => 'Lost',
    };
  }

  public function badgeClass(): string
  {
    return match ($this) {
      self::Open      => 'badge-blue',
      self::Responded => 'badge-yellow',
      self::Won       => 'badge-green',
      self::Lost      => 'badge-red',
    };
  }
}
