<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enquiries', function (Blueprint $table) {
            $table->decimal('quoted_price', 10, 2)->nullable()->after('assigned_sp_id');
            $table->foreignId('quoted_by')->nullable()->constrained('users')->nullOnDelete()->after('quoted_price');
            $table->timestamp('quoted_at')->nullable()->after('quoted_by');
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])->default('unpaid')->after('quoted_at');
            $table->string('stripe_session_id')->nullable()->after('payment_status');
            $table->string('stripe_payment_intent_id')->nullable()->after('stripe_session_id');
            $table->decimal('paid_amount', 10, 2)->nullable()->after('stripe_payment_intent_id');
            $table->timestamp('paid_at')->nullable()->after('paid_amount');
            $table->decimal('refunded_amount', 10, 2)->default(0)->after('paid_at');
        });
    }

    public function down(): void
    {
        Schema::table('enquiries', function (Blueprint $table) {
            $table->dropForeign(['quoted_by']);
            $table->dropColumn([
                'quoted_price', 'quoted_by', 'quoted_at',
                'payment_status', 'stripe_session_id', 'stripe_payment_intent_id',
                'paid_amount', 'paid_at', 'refunded_amount',
            ]);
        });
    }
};
