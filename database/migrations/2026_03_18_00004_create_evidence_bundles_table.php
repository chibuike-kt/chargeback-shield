<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('evidence_bundles', function (Blueprint $table) {
      $table->id();
      $table->ulid('ulid')->unique();
      $table->foreignId('transaction_id')->constrained('transactions')->cascadeOnDelete();
      $table->foreignId('merchant_id')->constrained('merchants')->cascadeOnDelete();

      // Encrypted payload
      $table->longText('payload_encrypted');
      $table->string('encryption_iv', 64);  // AES-256 IV (base64)

      // Integrity
      $table->string('hmac_signature', 128); // HMAC-SHA256 hex
      $table->boolean('is_verified')->default(false);

      // Immutability: no updated_at column intentionally
      $table->timestamp('created_at')->useCurrent();

      $table->index('transaction_id');
      $table->index('merchant_id');
      $table->index('hmac_signature');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('evidence_bundles');
  }
};
