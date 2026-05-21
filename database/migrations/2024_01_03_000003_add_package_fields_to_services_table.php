<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->boolean('has_premium_package')->default(false)->after('service_type');
            $table->text('basic_package_description')->nullable()->after('has_premium_package');
            $table->decimal('basic_package_price', 10, 2)->nullable()->after('basic_package_description');
            $table->text('premium_package_description')->nullable()->after('basic_package_price');
            $table->decimal('premium_package_price', 10, 2)->nullable()->after('premium_package_description');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn([
                'has_premium_package',
                'basic_package_description',
                'basic_package_price',
                'premium_package_description',
                'premium_package_price',
            ]);
        });
    }
};
