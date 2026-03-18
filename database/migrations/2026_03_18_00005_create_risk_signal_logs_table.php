<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('risk_signal_logs', function (Blueprint $table) {
      $table->id();
      $table->foreignId('transaction_id')->constrained('transactions')->cascadeOnDelete();

      $table->string('signal_name', 64);       // e.g. velocity_score
      $table->string('raw_value', 255);         // human-readable raw signal
      $table->decimal('normalized_score', 5, 4); // 0.0000 to 1.0000
      $table->decimal('weight', 5, 4);           // signal weight in composite
      $table->decimal('weighted_contribution', 5, 4); // normalized_score * weight

      $table->timestamp('created_at')->useCurrent();

      $table->index('transaction_id');
      $table->index('signal_name');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('risk_signal_logs');
  }
};
