<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE enquiries MODIFY COLUMN status ENUM('pending','in_progress','under_processing','resolved') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE enquiries MODIFY COLUMN status ENUM('pending','in_progress','resolved') NOT NULL DEFAULT 'pending'");
    }
};
