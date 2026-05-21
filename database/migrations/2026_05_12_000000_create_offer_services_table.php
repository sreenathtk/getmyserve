<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offer_services', function (Blueprint $table) {
            $table->foreignId('offer_id')->constrained('offers')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->primary(['offer_id', 'service_id']);
        });

        // Migrate existing single service mappings into the pivot table
        DB::statement('INSERT INTO offer_services (offer_id, service_id) SELECT id, service_id FROM offers WHERE service_id IS NOT NULL');

        Schema::table('offers', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropColumn('service_id');
        });
    }

    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->foreignId('service_id')->nullable()->constrained('services')->onDelete('cascade');
        });

        // Restore the first mapped service per offer
        DB::statement('UPDATE offers o SET service_id = (SELECT service_id FROM offer_services WHERE offer_id = o.id LIMIT 1)');

        Schema::dropIfExists('offer_services');
    }
};
