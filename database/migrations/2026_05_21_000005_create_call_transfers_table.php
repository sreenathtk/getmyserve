<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('call_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('call_log_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_agent_id')->nullable()->constrained('call_agents')->nullOnDelete();
            $table->foreignId('to_agent_id')->nullable()->constrained('call_agents')->nullOnDelete();
            $table->string('to_number', 30)->nullable();
            $table->enum('transfer_type', ['blind', 'attended'])->default('blind');
            $table->enum('status', ['initiated', 'completed', 'failed'])->default('initiated');
            $table->timestamp('initiated_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('call_log_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('call_transfers');
    }
};
