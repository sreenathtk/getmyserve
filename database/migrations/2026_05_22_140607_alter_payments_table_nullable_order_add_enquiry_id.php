<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE payments MODIFY COLUMN order_id BIGINT UNSIGNED NULL');

        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('enquiry_id')
                  ->nullable()
                  ->after('order_id')
                  ->constrained('enquiries')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['enquiry_id']);
            $table->dropColumn('enquiry_id');
        });

        DB::statement('ALTER TABLE payments MODIFY COLUMN order_id BIGINT UNSIGNED NOT NULL');
    }
};
