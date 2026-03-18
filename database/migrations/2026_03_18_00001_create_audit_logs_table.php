<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('audit_logs', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('merchant_id')->nullable();
      $table->string('actor_type')->default('merchant'); // merchant | system
      $table->string('action');
      $table->string('resource_type')->nullable();
      $table->string('resource_id')->nullable();
      $table->json('before_state')->nullable();
      $table->json('after_state')->nullable();
      $table->string('ip_address', 45)->nullable();
      $table->timestamp('created_at')->useCurrent();

      $table->index('merchant_id');
      $table->index(['resource_type', 'resource_id']);
      $table->index('created_at');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('audit_logs');
  }
};
