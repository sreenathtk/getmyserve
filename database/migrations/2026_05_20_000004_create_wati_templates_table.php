<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wati_templates', function (Blueprint $table) {
            $table->id();
            $table->string('template_key')->unique();   // our internal key e.g. customer_welcome
            $table->string('element_name')->unique();   // WATI template name e.g. getmyserve_welcome
            $table->string('description');              // human-readable purpose
            $table->string('recipient_type');           // customer | provider
            $table->text('body')->nullable();           // template body (pulled from WATI)
            $table->string('category')->nullable();     // UTILITY | MARKETING | AUTHENTICATION
            $table->string('language')->default('en');
            $table->string('status')->nullable();       // APPROVED | PENDING | REJECTED | PAUSED | DISABLED
            $table->text('rejection_reason')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wati_templates');
    }
};
