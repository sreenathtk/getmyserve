<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('status')->default('active')->after('combo_offer_id'); // active|cancelled
            $table->string('combo_offer_title')->nullable()->after('status');
            $table->decimal('combo_offer_price', 10, 2)->nullable()->after('combo_offer_title');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('discount_amount', 10, 2)->default(0)->after('amount');
            $table->json('offer_snapshot')->nullable()->after('discount_amount');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['status', 'combo_offer_title', 'combo_offer_price']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['discount_amount', 'offer_snapshot']);
        });
    }
};
