<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            // null means the staff member has access to ALL services in the category
            $table->foreignId('service_id')->nullable()->constrained('services')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'category_id', 'service_id'], 'staff_assignment_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_assignments');
    }
};
