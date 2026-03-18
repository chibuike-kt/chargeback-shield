<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('merchant_trust_registry', function (Blueprint $table) {
      $table->id();
      $table->foreignId('merchant_id')->constrained('merchants')->cascadeOnDelete();
      $table->foreignId('transaction_id')->nullable()->constrained('transactions')->nullOnDelete();

      $table->string('event_type');       // TrustEventType enum
      $table->unsignedInteger('penalty_points')->default(0);
      $table->text('notes')->nullable();

      // Append-only: created_at only, no updated_at
      $table->timestamp('created_at')->useCurrent();

      $table->index('merchant_id');
      $table->index('event_type');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('merchant_trust_registry');
  }
};
