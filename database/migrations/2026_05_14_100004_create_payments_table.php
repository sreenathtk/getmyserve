<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('stripe_payment_intent_id')->nullable()->index();
            $table->string('stripe_refund_id')->nullable()->index();
            $table->string('type'); // charge|refund
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('AED');
            $table->string('status'); // succeeded|pending|failed|canceled
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
