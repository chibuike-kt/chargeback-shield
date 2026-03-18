<?php

namespace App\Enums;

enum RiskLevel: string
{
  case Low    = 'low';
  case Medium = 'medium';
  case High   = 'high';

  public function label(): string
  {
    return match ($this) {
      self::Low    => 'Low Risk',
      self::Medium => 'Medium Risk',
      self::High   => 'High Risk',
    };
  }

  public function badgeClass(): string
  {
    return match ($this) {
      self::Low    => 'badge-green',
      self::Medium => 'badge-yellow',
      self::High   => 'badge-red',
    };
  }

  public static function fromScore(float $score): self
  {
    return match (true) {
      $score < 0.4  => self::Low,
      $score < 0.7  => self::Medium,
      default       => self::High,
    };
  }
}
