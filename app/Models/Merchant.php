<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Notifications\Notifiable;

class Merchant extends Authenticatable
{
  use HasUlids, Notifiable;

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

  /**
   * The primary key used by HasUlids is 'id' but we use
   * an auto-increment id + separate ulid column.
   * Override uniqueIds to return ulid only.
   */
  public function uniqueIds(): array
  {
    return ['ulid'];
  }
}
