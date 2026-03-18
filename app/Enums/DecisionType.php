<?php

namespace App\Enums;

enum DecisionType: string
{
  case Allow   = 'allow';
  case StepUp  = 'step_up';
  case Decline = 'decline';

  public function label(): string
  {
    return match ($this) {
      self::Allow   => 'Allowed',
      self::StepUp  => 'Step-Up (3DS)',
      self::Decline => 'Declined',
    };
  }

  public function badgeClass(): string
  {
    return match ($this) {
      self::Allow   => 'badge-green',
      self::StepUp  => 'badge-yellow',
      self::Decline => 'badge-red',
    };
  }

  public static function fromScore(float $score): self
  {
    return match (true) {
      $score < 0.4  => self::Allow,
      $score < 0.7  => self::StepUp,
      default       => self::Decline,
    };
  }
}
