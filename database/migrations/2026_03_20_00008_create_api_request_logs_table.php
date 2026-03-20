<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('api_request_logs', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('merchant_id')->nullable();
      $table->string('method', 10);
      $table->string('path');
      $table->unsignedSmallInteger('status_code');
      $table->string('ip_address', 45)->nullable();
      $table->unsignedInteger('response_time_ms')->nullable();
      $table->string('user_agent')->nullable();
      $table->timestamp('created_at')->useCurrent();

      $table->index('merchant_id');
      $table->index('status_code');
      $table->index('created_at');
      $table->index('ip_address');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('api_request_logs');
  }
};
