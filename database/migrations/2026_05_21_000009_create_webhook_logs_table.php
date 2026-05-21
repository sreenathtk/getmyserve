<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('source', 30)->default('ziwo');
            $table->string('event_type');
            $table->json('payload');
            $table->json('headers')->nullable();
            $table->string('signature')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_processed')->default(false);
            $table->timestamp('processed_at')->nullable();
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->text('last_error')->nullable();
            $table->string('idempotency_key')->unique();
            $table->timestamps();

            $table->index(['source', 'event_type']);
            $table->index(['is_processed', 'attempts']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_logs');
    }
};
