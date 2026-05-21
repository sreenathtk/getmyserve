<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        // Company Profile
        'company_name', 'company_logo', 'trade_license', 'license_expiry_date',
        'business_activity', 'company_type', 'website', 'company_email',
        // Primary Contact
        'primary_contact_name', 'primary_contact_mobile', 'primary_contact_email',
        'primary_contact_language', 'primary_contact_designation',
        // Secondary Contact
        'secondary_contact_name', 'secondary_contact_mobile', 'secondary_contact_email',
        'secondary_contact_language', 'secondary_contact_designation',
        // Location
        'head_office_address', 'emirate_city', 'google_map_pin', 'branch_details',
        // Business Operations
        'working_days', 'working_hours_from', 'working_hours_to',
        // Bank Details
        'bank_name', 'account_name', 'iban_account', 'bank_branch', 'swift_code',
        // Communication
        'comm_mobile', 'comm_language', 'whatsapp_number',
        // Compliances
        'kyc_status', 'verified_by', 'approval_status',
        // Documents
        'trade_license_copy', 'emirates_id_doc', 'passport_copy', 'visa_copy',
        'vat_certificate', 'ejari_contract', 'owner_emirates_id', 'company_stamp',
        'signboard_image',
        // Status
        'is_active', 'is_trusted',
    ];

    protected $casts = [
        'license_expiry_date' => 'date',
        'is_active' => 'boolean',
        'is_trusted' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_provider_services')
            ->withPivot(['turnaround_time', 'b2b_price', 'markup_percent', 'market_price', 'final_price', 'remarks', 'is_active'])
            ->withTimestamps();
    }
}
