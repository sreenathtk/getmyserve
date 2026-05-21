<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('call_recordings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('call_log_id')->constrained()->cascadeOnDelete();
            $table->string('ziwo_recording_id')->unique();
            $table->text('recording_url');
            $table->string('storage_path')->nullable();
            $table->string('storage_disk', 20)->default('local');
            $table->unsignedBigInteger('file_size_bytes')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->string('format', 10)->default('mp3');
            $table->boolean('is_downloaded')->default(false);
            $table->timestamp('downloaded_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index('call_log_id');
            $table->index('is_downloaded');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('call_recordings');
    }
};
