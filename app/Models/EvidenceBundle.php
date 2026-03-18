<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvidenceBundle extends Model
{
  use HasUlids, HasFactory;

  /**
   * Immutable — no updated_at column exists on this table.
   * We disable Laravel's automatic timestamp management entirely
   * and handle created_at manually.
   */
  public $timestamps = false;

  protected $fillable = [
    'ulid',
    'transaction_id',
    'merchant_id',
    'payload_encrypted',
    'encryption_iv',
    'hmac_signature',
    'is_verified',
    'created_at',
  ];

  protected $casts = [
    'is_verified' => 'boolean',
    'created_at'  => 'datetime',
  ];

  public function uniqueIds(): array
  {
    return ['ulid'];
  }

    // ── Immutability enforcement ──────────────────────────────────────────────

  /**
   * Block all updates at the model level.
   * Evidence bundles are write-once.
   */
  public function save(array $options = []): bool
  {
    if ($this->exists) {
      throw new \RuntimeException(
        'EvidenceBundle is immutable. Updates are not permitted after creation.'
      );
    }

    $this->created_at = now();

    return parent::save($options);
  }

  // ── Relationships ─────────────────────────────────────────────────────────

  public function transaction(): BelongsTo
  {
    return $this->belongsTo(Transaction::class);
  }

  public function merchant(): BelongsTo
  {
    return $this->belongsTo(Merchant::class);
  }
}
