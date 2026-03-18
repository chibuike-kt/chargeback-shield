<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('merchants', function (Blueprint $table) {
      $table->id();
      $table->ulid('ulid')->unique();
      $table->string('company_name');
      $table->string('email')->unique();
      $table->string('password');
      $table->string('api_key', 64)->unique();
      $table->string('webhook_secret', 64);
      $table->string('webhook_url')->nullable();
      $table->boolean('is_active')->default(true);
      $table->rememberToken();
      $table->timestamps();

      $table->index('api_key');
      $table->index('email');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('merchants');
  }
};
