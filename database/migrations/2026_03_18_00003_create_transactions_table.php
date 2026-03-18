<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('transactions', function (Blueprint $table) {
      $table->id();
      $table->ulid('ulid')->unique();
      $table->foreignId('merchant_id')->constrained('merchants')->cascadeOnDelete();
      $table->string('idempotency_key', 128)->unique();

      // Card details (masked)
      $table->string('card_bin', 8);
      $table->string('card_last4', 4);
      $table->string('card_country', 2)->nullable();

      // Transaction details
      $table->unsignedBigInteger('amount'); // stored in minor units (kobo/cents)
      $table->string('currency', 3)->default('NGN');

      // Network signals
      $table->string('ip_address', 45)->nullable();
      $table->string('ip_country', 2)->nullable();
      $table->string('ip_city')->nullable();

      // Device and session
      $table->string('device_fingerprint', 128)->nullable();
      $table->string('session_token', 128)->nullable();
      $table->unsignedInteger('session_age_seconds')->default(0);

      // Merchant context
      $table->string('merchant_category', 10)->nullable(); // MCC code

      // Scoring output
      $table->decimal('risk_score', 5, 4)->default(0.0000);
      $table->string('risk_level')->default('low');   // RiskLevel enum
      $table->string('decision')->default('allow');   // DecisionType enum
      $table->string('status')->default('pending');   // TransactionStatus enum

      // Evidence
      $table->unsignedBigInteger('evidence_bundle_id')->nullable();

      $table->timestamps();

      $table->index('merchant_id');
      $table->index('idempotency_key');
      $table->index('card_bin');
      $table->index('device_fingerprint');
      $table->index('status');
      $table->index('decision');
      $table->index('created_at');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('transactions');
  }
};
