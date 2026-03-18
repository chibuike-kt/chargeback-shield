<?php

namespace App\Models;

use App\Enums\ActorType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
  public $timestamps = false;

  protected $fillable = [
    'merchant_id',
    'actor_type',
    'action',
    'resource_type',
    'resource_id',
    'before_state',
    'after_state',
    'ip_address',
    'created_at',
  ];

  protected $casts = [
    'actor_type'   => ActorType::class,
    'before_state' => 'array',
    'after_state'  => 'array',
    'created_at'   => 'datetime',
  ];

  public function merchant(): BelongsTo
  {
    return $this->belongsTo(Merchant::class);
  }
}
