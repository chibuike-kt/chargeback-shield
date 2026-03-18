<?php

namespace App\Models;

use App\Enums\DisputeNetwork;
use App\Enums\DisputeStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dispute extends Model
{
  use HasUlids, HasFactory;

  protected $fillable = [
    'ulid',
    'transaction_id',
    'merchant_id',
    'reason_code',
    'reason_description',
    'network',
    'status',
    'response_document',
    'pdf_path',
    'filed_at',
    'responded_at',
    'resolved_at',
  ];

  protected $casts = [
    'network'           => DisputeNetwork::class,
    'status'            => DisputeStatus::class,
    'response_document' => 'array',
    'filed_at'          => 'datetime',
    'responded_at'      => 'datetime',
    'resolved_at'       => 'datetime',
  ];

  public function uniqueIds(): array
  {
    return ['ulid'];
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

  public function webhookDeliveries(): HasMany
  {
    return $this->hasMany(WebhookDelivery::class);
  }

  // ── Helpers ───────────────────────────────────────────────────────────────

  public function isResolved(): bool
  {
    return in_array($this->status, [DisputeStatus::Won, DisputeStatus::Lost]);
  }

  public function hasPdfResponse(): bool
  {
    return $this->pdf_path !== null;
  }
}
