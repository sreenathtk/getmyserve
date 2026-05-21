<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('communication_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('communication_history_id')->constrained()->cascadeOnDelete();
            $table->morphs('linkable');
            $table->timestamps();

            $table->unique(
                ['communication_history_id', 'linkable_type', 'linkable_id'],
                'comm_link_unique'
            );
            $table->index(['linkable_type', 'linkable_id', 'created_at'], 'comm_links_timeline_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('communication_links');
    }
};
