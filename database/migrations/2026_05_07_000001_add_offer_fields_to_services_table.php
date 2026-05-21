<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->decimal('basic_package_actual_price', 10, 2)->nullable()->after('basic_package_price');
            $table->enum('basic_package_discount_type', ['percentage', 'flat'])->nullable()->after('basic_package_actual_price');
            $table->decimal('basic_package_discount_value', 10, 2)->nullable()->after('basic_package_discount_type');
            $table->decimal('premium_package_actual_price', 10, 2)->nullable()->after('premium_package_price');
            $table->enum('premium_package_discount_type', ['percentage', 'flat'])->nullable()->after('premium_package_actual_price');
            $table->decimal('premium_package_discount_value', 10, 2)->nullable()->after('premium_package_discount_type');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn([
                'basic_package_actual_price',
                'basic_package_discount_type',
                'basic_package_discount_value',
                'premium_package_actual_price',
                'premium_package_discount_type',
                'premium_package_discount_value',
            ]);
        });
    }
};
