<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->decimal('original_price', 10, 2)->nullable()->after('price');
            $table->unsignedBigInteger('combo_offer_id')->nullable()->after('original_price');
            $table->string('combo_group', 100)->nullable()->after('combo_offer_id');
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropColumn(['original_price', 'combo_offer_id', 'combo_group']);
        });
    }
};
