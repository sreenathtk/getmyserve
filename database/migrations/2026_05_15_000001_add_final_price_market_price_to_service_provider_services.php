<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_provider_services', function (Blueprint $table) {
            $table->decimal('market_price', 10, 2)->nullable()->after('markup_percent');
            $table->decimal('final_price', 10, 2)->nullable()->after('market_price');
        });
    }

    public function down(): void
    {
        Schema::table('service_provider_services', function (Blueprint $table) {
            $table->dropColumn(['market_price', 'final_price']);
        });
    }
};
