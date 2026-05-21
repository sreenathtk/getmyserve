<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('call_logs', function (Blueprint $table) {
            $table->id();
            $table->string('ziwo_call_id')->unique();
            $table->enum('direction', ['inbound', 'outbound']);
            $table->enum('status', [
                'initiated', 'ringing', 'answered', 'ended', 'missed', 'failed', 'transferred',
            ])->default('initiated');
            $table->string('caller_number', 30);
            $table->string('callee_number', 30);
            $table->string('caller_name')->nullable();
            $table->string('callee_name')->nullable();
            $table->foreignId('agent_id')->nullable()->constrained('call_agents')->nullOnDelete();
            $table->string('queue_name')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('answered_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->unsignedInteger('duration_seconds')->default(0);
            $table->unsignedInteger('hold_duration_seconds')->default(0);
            $table->unsignedInteger('talk_duration_seconds')->default(0);
            $table->string('hangup_cause')->nullable();
            $table->json('ziwo_metadata')->nullable();
            $table->timestamps();

            $table->index('agent_id');
            $table->index('caller_number');
            $table->index('callee_number');
            $table->index('started_at');
            $table->index('status');
            $table->index('direction');
            $table->index(['status', 'direction', 'started_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('call_logs');
    }
};
