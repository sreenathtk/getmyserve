<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('call_agents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ziwo_agent_id')->unique();
            $table->string('ziwo_extension')->nullable();
            $table->string('ziwo_username');
            $table->string('display_name');
            $table->enum('status', ['online', 'offline', 'busy', 'on_call', 'paused'])->default('offline');
            $table->timestamp('last_status_changed_at')->nullable();
            $table->unsignedInteger('total_calls_today')->default(0);
            $table->unsignedInteger('total_talk_seconds_today')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['status', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('call_agents');
    }
};
