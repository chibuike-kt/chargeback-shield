<?php

namespace App\Models;

use App\Enums\WebhookEventType;
use App\Enums\WebhookStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebhookDelivery extends Model
{
  use HasUlids, HasFactory;

  protected $fillable = [
    'ulid',
    'merchant_id',
    'transaction_id',
    'dispute_id',
    'event_type',
    'payload',
    'url',
    'http_status',
    'response_body',
    'attempt_number',
    'status',
    'next_retry_at',
  ];

  protected $casts = [
    'event_type'    => WebhookEventType::class,
    'status'        => WebhookStatus::class,
    'payload'       => 'array',
    'http_status'   => 'integer',
    'attempt_number' => 'integer',
    'next_retry_at' => 'datetime',
  ];

  public function uniqueIds(): array
  {
    return ['ulid'];
  }

  // ── Relationships ─────────────────────────────────────────────────────────

  public function merchant(): BelongsTo
  {
    return $this->belongsTo(Merchant::class);
  }

  public function transaction(): BelongsTo
  {
    return $this->belongsTo(Transaction::class);
  }

  public function dispute(): BelongsTo
  {
    return $this->belongsTo(Dispute::class);
  }

  // ── Helpers ───────────────────────────────────────────────────────────────

  public function isDelivered(): bool
  {
    return $this->status === WebhookStatus::Delivered;
  }

  public function canRetry(): bool
  {
    return $this->status === WebhookStatus::Failed
      && $this->attempt_number < 3;
  }
}
