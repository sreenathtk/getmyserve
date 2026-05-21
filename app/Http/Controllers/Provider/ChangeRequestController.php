<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceProviderChangeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChangeRequestController extends Controller
{
    public function create()
    {
        $serviceProvider = Auth::user()->serviceProvider()->with('services')->firstOrFail();
        $services = Service::where('is_active', true)->orderBy('name')->get();

        return view('provider.change-requests.create', compact('serviceProvider', 'services'));
    }

    public function store(Request $request)
    {
        $serviceProvider = Auth::user()->serviceProvider()->firstOrFail();

        $request->validate([
            'company_name' => 'required|string|max:255',
            'primary_contact_name' => 'required|string|max:255',
            'primary_contact_mobile' => 'required|string|max:20',
        ]);

        $payload = [
            'user' => [],
            'provider' => [
                'company_name' => $request->company_name,
                'trade_license' => $request->trade_license,
                'license_expiry_date' => $request->license_expiry_date,
                'business_activity' => $request->business_activity,
                'company_type' => $request->company_type,
                'website' => $request->website,
                'company_email' => $request->company_email,
                'primary_contact_name' => $request->primary_contact_name,
                'primary_contact_mobile' => $request->primary_contact_mobile,
                'primary_contact_email' => $request->primary_contact_email,
                'primary_contact_language' => $request->primary_contact_language,
                'primary_contact_designation' => $request->primary_contact_designation,
                'secondary_contact_name' => $request->secondary_contact_name,
                'secondary_contact_mobile' => $request->secondary_contact_mobile,
                'secondary_contact_email' => $request->secondary_contact_email,
                'secondary_contact_language' => $request->secondary_contact_language,
                'secondary_contact_designation' => $request->secondary_contact_designation,
                'head_office_address' => $request->head_office_address,
                'emirate_city' => $request->emirate_city,
                'google_map_pin' => $request->google_map_pin,
                'branch_details' => $request->branch_details,
                'working_days' => $request->working_days,
                'working_hours_from' => $request->working_hours_from,
                'working_hours_to' => $request->working_hours_to,
                'bank_name' => $request->bank_name,
                'account_name' => $request->account_name,
                'iban_account' => $request->iban_account,
                'bank_branch' => $request->bank_branch,
                'swift_code' => $request->swift_code,
                'comm_mobile' => $request->comm_mobile,
                'comm_language' => $request->comm_language,
                'kyc_status' => $serviceProvider->kyc_status,
                'verified_by' => $serviceProvider->verified_by,
                'emirates_id_doc' => $request->emirates_id_doc,
                'owner_emirates_id' => $request->owner_emirates_id,
            ],
            'services' => [],
            'documents' => [],
        ];

        if ($request->has('services')) {
            foreach ($request->services as $index => $serviceId) {
                if ($serviceId) {
                    $payload['services'][] = [
                        'service_id'      => $serviceId,
                        'turnaround_time' => $request->turnaround_times[$index] ?? null,
                        'b2b_price'       => $request->b2b_prices[$index] ?? null,
                        'remarks'         => $request->service_remarks[$index] ?? null,
                    ];
                }
            }
        }

        $changeRequest = ServiceProviderChangeRequest::create([
            'requested_by_user_id' => Auth::id(),
            'request_type' => 'update',
            'service_provider_id' => $serviceProvider->id,
            'payload' => $payload,
            'status' => 'pending',
        ]);

        $fileFields = [
            'company_logo', 'trade_license_copy', 'passport_copy',
            'visa_copy', 'vat_certificate', 'ejari_contract',
            'company_stamp', 'signboard_image',
        ];

        $documents = [];
        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $path = $request->file($field)->store('change-requests/' . $changeRequest->id, 'public');
                $documents[$field] = $path;
            }
        }

        if (!empty($documents)) {
            $payload['documents'] = $documents;
            $changeRequest->update(['payload' => $payload]);
        }

        return redirect()->route('provider.dashboard')
            ->with('success', 'Profile update request submitted. Awaiting admin approval.');
    }
}
