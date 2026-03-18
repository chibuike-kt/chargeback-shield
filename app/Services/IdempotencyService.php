<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;

class IdempotencyService
{
  private const PREFIX  = 'idempotency:';
  private const TTL_SECONDS = 86400; // 24 hours

  /**
   * Check if a key has already been processed.
   * Returns the cached response array or null.
   */
  public function get(string $merchantId, string $idempotencyKey): ?array
  {
    $value = Redis::get($this->buildKey($merchantId, $idempotencyKey));

    if (!$value) {
      return null;
    }

    return json_decode($value, true);
  }

  /**
   * Store the response for a given idempotency key.
   */
  public function store(string $merchantId, string $idempotencyKey, array $response): void
  {
    Redis::setex(
      $this->buildKey($merchantId, $idempotencyKey),
      self::TTL_SECONDS,
      json_encode($response)
    );
  }

  /**
   * Check existence without retrieving the value.
   */
  public function exists(string $merchantId, string $idempotencyKey): bool
  {
    return (bool) Redis::exists($this->buildKey($merchantId, $idempotencyKey));
  }

  private function buildKey(string $merchantId, string $idempotencyKey): string
  {
    return self::PREFIX . $merchantId . ':' . hash('sha256', $idempotencyKey);
  }
}
