<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_providers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Company Profile
            $table->string('company_name');
            $table->string('company_logo')->nullable();
            $table->string('trade_license')->nullable();
            $table->date('license_expiry_date')->nullable();
            $table->string('business_activity')->nullable();
            $table->string('company_type')->nullable();
            $table->string('website')->nullable();
            $table->string('company_email')->nullable();

            // Primary Contact
            $table->string('primary_contact_name')->nullable();
            $table->string('primary_contact_mobile')->nullable();
            $table->string('primary_contact_email')->nullable();
            $table->string('primary_contact_language')->nullable();
            $table->string('primary_contact_designation')->nullable();

            // Secondary Contact
            $table->string('secondary_contact_name')->nullable();
            $table->string('secondary_contact_mobile')->nullable();
            $table->string('secondary_contact_email')->nullable();
            $table->string('secondary_contact_language')->nullable();
            $table->string('secondary_contact_designation')->nullable();

            // Location Details
            $table->text('head_office_address')->nullable();
            $table->string('emirate_city')->nullable();
            $table->string('google_map_pin')->nullable();
            $table->text('branch_details')->nullable();

            // Business Operations
            $table->string('working_days')->nullable();
            $table->time('working_hours_from')->nullable();
            $table->time('working_hours_to')->nullable();

            // Bank Details
            $table->string('bank_name')->nullable();
            $table->string('account_name')->nullable();
            $table->string('iban_account')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('swift_code')->nullable();

            // Communication
            $table->string('comm_mobile')->nullable();
            $table->string('comm_language')->nullable();

            // Compliances & Verification
            $table->enum('kyc_status', ['yes', 'no'])->default('no');
            $table->string('verified_by')->nullable();
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending');

            // Document uploads (file paths)
            $table->string('trade_license_copy')->nullable();
            $table->string('emirates_id_doc')->nullable();
            $table->string('passport_copy')->nullable();
            $table->string('visa_copy')->nullable();
            $table->string('vat_certificate')->nullable();
            $table->string('ejari_contract')->nullable();
            $table->string('owner_emirates_id')->nullable();
            $table->string('company_stamp')->nullable();
            $table->string('signboard_image')->nullable();

            // Status
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_providers');
    }
};
