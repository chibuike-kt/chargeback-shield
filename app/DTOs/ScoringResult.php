<?php

namespace App\DTOs;

use App\Enums\DecisionType;
use App\Enums\RiskLevel;

class ScoringResult
{
  public function __construct(
    public readonly float        $score,
    public readonly RiskLevel    $riskLevel,
    public readonly DecisionType $decision,
    public readonly array        $signals,  // individual signal breakdowns
  ) {}

  public static function fromArray(array $data): self
  {
    return new self(
      score: $data['score'],
      riskLevel: RiskLevel::fromScore($data['score']),
      decision: DecisionType::fromScore($data['score']),
      signals: $data['signals'] ?? [],
    );
  }

  public function toArray(): array
  {
    return [
      'score'      => $this->score,
      'risk_level' => $this->riskLevel->value,
      'decision'   => $this->decision->value,
      'signals'    => $this->signals,
    ];
  }
}
