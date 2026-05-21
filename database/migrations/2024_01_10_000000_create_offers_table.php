<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->enum('offer_type', ['limited_offer', 'best_value', 'flash_sale', 'exclusive', 'seasonal'])->default('limited_offer');
            $table->string('title');
            $table->decimal('offer_price', 10, 2);
            $table->text('offer_detail')->nullable();
            $table->string('button_text')->default('Get Started');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
