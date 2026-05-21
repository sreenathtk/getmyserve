<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('communication_histories', function (Blueprint $table) {
            $table->id();
            $table->enum('channel', ['call', 'whatsapp', 'email', 'note', 'system']);
            $table->enum('direction', ['inbound', 'outbound', 'internal'])->default('internal');
            $table->string('summary');
            $table->text('body')->nullable();
            $table->foreignId('agent_id')->nullable()->constrained('call_agents')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->nullableMorphs('reference');
            $table->timestamp('happened_at');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('channel');
            $table->index('happened_at');
            // nullableMorphs('reference') already creates the (reference_type, reference_id) index
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('communication_histories');
    }
};
