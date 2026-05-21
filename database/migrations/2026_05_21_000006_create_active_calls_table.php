<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('active_calls', function (Blueprint $table) {
            $table->id();
            $table->string('ziwo_call_id')->unique();
            $table->foreignId('call_log_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('agent_id')->nullable()->constrained('call_agents')->nullOnDelete();
            $table->string('caller_number', 30);
            $table->string('callee_number', 30);
            $table->enum('status', ['ringing', 'answered', 'on_hold', 'transferring'])->default('ringing');
            $table->timestamp('started_at');
            $table->timestamp('last_updated_at');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('agent_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('active_calls');
    }
};
