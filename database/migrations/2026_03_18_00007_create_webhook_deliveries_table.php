<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('webhook_deliveries', function (Blueprint $table) {
      $table->id();
      $table->ulid('ulid')->unique();
      $table->foreignId('merchant_id')->constrained('merchants')->cascadeOnDelete();
      $table->foreignId('transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
      $table->foreignId('dispute_id')->nullable()->constrained('disputes')->nullOnDelete();

      $table->string('event_type');          // WebhookEventType enum
      $table->json('payload');
      $table->string('url');
      $table->unsignedSmallInteger('http_status')->nullable();
      $table->text('response_body')->nullable();
      $table->unsignedTinyInteger('attempt_number')->default(1);
      $table->string('status')->default('pending'); // WebhookStatus enum
      $table->timestamp('next_retry_at')->nullable();

      $table->timestamps();

      $table->index('merchant_id');
      $table->index('status');
      $table->index('event_type');
      $table->index('next_retry_at');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('webhook_deliveries');
  }
};
