<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('assigned_staff_id')
                ->nullable()->constrained('users')->nullOnDelete()
                ->after('user_id');
            $table->text('notes')->nullable()->after('items');
            $table->string('refund_status')->default('none')->after('notes'); // none|requested|partial|full
            $table->decimal('refunded_amount', 10, 2)->default(0)->after('refund_status');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['assigned_staff_id']);
            $table->dropColumn(['assigned_staff_id', 'notes', 'refund_status', 'refunded_amount']);
        });
    }
};
