<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiskSignalLog extends Model
{
  public $timestamps = false;

  protected $fillable = [
    'transaction_id',
    'signal_name',
    'raw_value',
    'normalized_score',
    'weight',
    'weighted_contribution',
    'created_at',
  ];

  protected $casts = [
    'normalized_score'     => 'float',
    'weight'               => 'float',
    'weighted_contribution' => 'float',
    'created_at'           => 'datetime',
  ];

  public function transaction(): BelongsTo
  {
    return $this->belongsTo(Transaction::class);
  }
}
