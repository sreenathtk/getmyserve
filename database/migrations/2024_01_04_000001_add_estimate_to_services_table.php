<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->unsignedSmallInteger('estimate_value')->nullable()->after('service_type');
            $table->enum('estimate_unit', ['hours', 'days', 'weeks'])->nullable()->after('estimate_value');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['estimate_value', 'estimate_unit']);
        });
    }
};
