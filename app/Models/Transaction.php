<?php

namespace App\Models;

use App\Enums\DecisionType;
use App\Enums\RiskLevel;
use App\Enums\TransactionStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaction extends Model
{
  use HasUlids, HasFactory;

  protected $fillable = [
    'ulid',
    'merchant_id',
    'idempotency_key',
    'card_bin',
    'card_last4',
    'card_country',
    'amount',
    'currency',
    'ip_address',
    'ip_country',
    'ip_city',
    'device_fingerprint',
    'session_token',
    'session_age_seconds',
    'merchant_category',
    'risk_score',
    'risk_level',
    'decision',
    'status',
    'evidence_bundle_id',
  ];

  protected $casts = [
    'risk_score'          => 'float',
    'risk_level'          => RiskLevel::class,
    'decision'            => DecisionType::class,
    'status'              => TransactionStatus::class,
    'session_age_seconds' => 'integer',
    'amount'              => 'integer',
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

  public function evidenceBundle(): HasOne
  {
    return $this->hasOne(EvidenceBundle::class);
  }

  public function riskSignalLogs(): HasMany
  {
    return $this->hasMany(RiskSignalLog::class);
  }

  public function dispute(): HasOne
  {
    return $this->hasOne(Dispute::class);
  }

  public function webhookDeliveries(): HasMany
  {
    return $this->hasMany(WebhookDelivery::class);
  }

  // ── Helpers ───────────────────────────────────────────────────────────────

  public function formattedAmount(): string
  {
    return number_format($this->amount / 100, 2);
  }

  public function isApproved(): bool
  {
    return $this->status === TransactionStatus::Approved;
  }

  public function hasEvidence(): bool
  {
    return $this->evidence_bundle_id !== null;
  }
}
