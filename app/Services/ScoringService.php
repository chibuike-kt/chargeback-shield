<?php

namespace App\Services;

use App\DTOs\ScoringResult;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ScoringService
{
  private string $nodeServiceUrl;

  public function __construct()
  {
    $this->nodeServiceUrl = config('services.scoring.url', 'http://localhost:3001');
  }

  /**
   * Score a transaction.
   * Calls the Node.js scoring service.
   * Falls back to stub scoring if Node.js is unavailable.
   */
  public function scoreTransaction(array $transactionData): ScoringResult
  {
    try {
      $response = Http::timeout(3)
        ->post("{$this->nodeServiceUrl}/score", $transactionData);

      if ($response->successful()) {
        return ScoringResult::fromArray($response->json());
      }

      Log::warning('Scoring service returned non-200', [
        'status' => $response->status(),
        'body'   => $response->body(),
      ]);
    } catch (\Exception $e) {
      Log::warning('Scoring service unreachable, using stub scorer', [
        'error' => $e->getMessage(),
      ]);
    }

    // Fallback: stub scorer
    return $this->stubScore($transactionData);
  }

  /**
   * Stub scorer — used when Node.js service is not yet running.
   * Produces realistic scores based on available signals.
   * Phase 4 makes this the real thing.
   */
  private function stubScore(array $data): ScoringResult
  {
    $signals = [];
    $score   = 0.0;

    // Signal 1: Geo mismatch (card country vs IP country)
    $geoScore = 0.1;
    if (!empty($data['card_country']) && !empty($data['ip_country'])) {
      $geoScore = $data['card_country'] !== $data['ip_country'] ? 0.75 : 0.1;
    }
    $signals[] = $this->buildSignal('geo_mismatch', $data['card_country'] ?? 'unknown', $geoScore, 0.20);

    // Signal 2: Device fingerprint (new device = higher risk)
    $deviceScore = empty($data['device_fingerprint']) ? 0.65 : 0.15;
    $signals[]   = $this->buildSignal('device_fingerprint', $data['device_fingerprint'] ?? 'none', $deviceScore, 0.20);

    // Signal 3: Session age (very new session = higher risk)
    $sessionAge   = $data['session_age_seconds'] ?? 0;
    $sessionScore = match (true) {
      $sessionAge === 0        => 0.80,
      $sessionAge < 60         => 0.60,
      $sessionAge < 300        => 0.35,
      default                  => 0.10,
    };
    $signals[] = $this->buildSignal('session_age', "{$sessionAge}s", $sessionScore, 0.15);

    // Signal 4: Amount risk (high amounts = slightly higher risk)
    $amount      = $data['amount'] ?? 0;
    $amountScore = match (true) {
      $amount > 10_000_000 => 0.70,  // > 100k NGN
      $amount > 5_000_000  => 0.45,  // > 50k NGN
      $amount > 1_000_000  => 0.25,  // > 10k NGN
      default              => 0.10,
    };
    $signals[] = $this->buildSignal('amount_risk', number_format($amount / 100, 2), $amountScore, 0.15);

    // Signal 5: BIN risk (stub — a few high-risk BIN prefixes)
    $bin         = substr($data['card_bin'] ?? '000000', 0, 3);
    $highRiskBins = ['490', '491', '556', '670', '671'];
    $binScore    = in_array($bin, $highRiskBins) ? 0.75 : 0.10;
    $signals[]   = $this->buildSignal('bin_risk', $data['card_bin'] ?? 'unknown', $binScore, 0.15);

    // Signal 6: Velocity (stub — Phase 4 wires real Redis windows)
    $velocityScore = 0.10;
    $signals[]     = $this->buildSignal('velocity', 'stub:0_flags', $velocityScore, 0.15);

    // Composite score = sum of (normalized_score * weight)
    foreach ($signals as $signal) {
      $score += $signal['weighted_contribution'];
    }

    $score = min(1.0, round($score, 4));

    return ScoringResult::fromArray([
      'score'   => $score,
      'signals' => $signals,
    ]);
  }

  private function buildSignal(
    string $name,
    string $rawValue,
    float $normalizedScore,
    float $weight
  ): array {
    return [
      'signal_name'          => $name,
      'raw_value'            => $rawValue,
      'normalized_score'     => round($normalizedScore, 4),
      'weight'               => $weight,
      'weighted_contribution' => round($normalizedScore * $weight, 4),
    ];
  }
}
