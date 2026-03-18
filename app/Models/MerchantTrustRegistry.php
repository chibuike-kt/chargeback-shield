<?php

namespace App\Models;

use App\Enums\TrustEventType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MerchantTrustRegistry extends Model
{
  use HasFactory;

  public $timestamps = false;

  protected $table = 'merchant_trust_registry';

  protected $fillable = [
    'merchant_id',
    'transaction_id',
    'event_type',
    'penalty_points',
    'notes',
    'created_at',
  ];

  protected $casts = [
    'event_type'     => TrustEventType::class,
    'penalty_points' => 'integer',
    'created_at'     => 'datetime',
  ];

  public function save(array $options = []): bool
  {
    if ($this->exists) {
      throw new \RuntimeException(
        'MerchantTrustRegistry is append-only. Updates are not permitted.'
      );
    }

    $this->created_at = now();

    return parent::save($options);
  }

  public function merchant(): BelongsTo
  {
    return $this->belongsTo(Merchant::class);
  }

  public function transaction(): BelongsTo
  {
    return $this->belongsTo(Transaction::class);
  }
}
