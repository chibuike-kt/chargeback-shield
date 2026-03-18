<?php

namespace App\Models;

use App\Enums\ActorType;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Merchant extends Authenticatable
{
  use HasUlids, HasFactory, Notifiable;

  protected $fillable = [
    'ulid',
    'company_name',
    'email',
    'password',
    'api_key',
    'webhook_secret',
    'webhook_url',
    'is_active',
  ];

  protected $hidden = [
    'password',
    'webhook_secret',
    'remember_token',
  ];

  protected $casts = [
    'is_active'         => 'boolean',
    'email_verified_at' => 'datetime',
    'password'          => 'hashed',
  ];

  public function uniqueIds(): array
  {
    return ['ulid'];
  }

  // ── Relationships ─────────────────────────────────────────────────────────

  public function transactions(): HasMany
  {
    return $this->hasMany(Transaction::class);
  }

  public function evidenceBundles(): HasMany
  {
    return $this->hasMany(EvidenceBundle::class);
  }

  public function disputes(): HasMany
  {
    return $this->hasMany(Dispute::class);
  }

  public function webhookDeliveries(): HasMany
  {
    return $this->hasMany(WebhookDelivery::class);
  }

  public function trustRegistry(): HasMany
  {
    return $this->hasMany(MerchantTrustRegistry::class);
  }

  public function auditLogs(): HasMany
  {
    return $this->hasMany(AuditLog::class);
  }

  // ── Computed helpers ──────────────────────────────────────────────────────

  public function totalPenaltyPoints(): int
  {
    return $this->trustRegistry()->sum('penalty_points');
  }

  public function trustScore(): float
  {
    // Score between 0 (bad) and 1 (good)
    // Starts at 1.0, decreases with penalty points
    $penalty = $this->totalPenaltyPoints();
    return max(0.0, round(1.0 - ($penalty / 200), 4));
  }
}
