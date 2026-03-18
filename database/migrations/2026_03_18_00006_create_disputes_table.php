<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('disputes', function (Blueprint $table) {
      $table->id();
      $table->ulid('ulid')->unique();
      $table->foreignId('transaction_id')->constrained('transactions')->cascadeOnDelete();
      $table->foreignId('merchant_id')->constrained('merchants')->cascadeOnDelete();

      // Chargeback details
      $table->string('reason_code', 20);         // e.g. 4853, 10.4
      $table->string('reason_description')->nullable();
      $table->string('network')->default('visa'); // DisputeNetwork enum
      $table->string('status')->default('open'); // DisputeStatus enum

      // Response
      $table->json('response_document')->nullable();
      $table->string('pdf_path')->nullable();

      // Timestamps
      $table->timestamp('filed_at')->nullable();
      $table->timestamp('responded_at')->nullable();
      $table->timestamp('resolved_at')->nullable();
      $table->timestamps();

      $table->index('merchant_id');
      $table->index('transaction_id');
      $table->index('status');
      $table->index('reason_code');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('disputes');
  }
};
