<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceProvider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProviderRegistrationController extends Controller
{
    public function create()
    {
        $services = Service::with('subCategory.category')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $grouped = [];
        foreach ($services as $s) {
            $cat = optional(optional($s->subCategory)->category)->name ?? 'General';
            $sub = optional($s->subCategory)->name ?? 'General';
            $grouped[$cat][$sub][] = $s;
        }

        return view('auth.provider-register', compact('grouped'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'email'                  => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'               => ['required', 'confirmed', Password::min(8)],
            'company_name'           => ['required', 'string', 'max:255'],
            'trade_license'          => ['required', 'string', 'max:100'],
            'license_expiry_date'    => ['required', 'date'],
            'head_office_address'    => ['required', 'string', 'max:500'],
            'emirate_city'           => ['required', 'string', 'max:100'],
            'primary_contact_name'   => ['required', 'string', 'max:255'],
            'primary_contact_mobile' => ['required', 'string', 'max:20'],
            'trade_license_copy'     => ['required', 'file', 'max:5120', 'mimes:pdf,jpg,jpeg,png'],
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name'      => $request->input('company_name'),
                'email'     => $request->input('email'),
                'phone'     => $request->input('primary_contact_mobile', ''),
                'password'  => Hash::make($request->input('password')),
                'role'      => User::ROLE_SERVICE_PROVIDER,
                'is_active' => false,
            ]);

            $fileData   = [];
            $fileFields = [
                'company_logo', 'trade_license_copy', 'passport_copy',
                'visa_copy', 'vat_certificate', 'ejari_contract',
                'company_stamp', 'signboard_image',
            ];
            foreach ($fileFields as $field) {
                if ($request->hasFile($field) && $request->file($field)->isValid()) {
                    $fileData[$field] = $request->file($field)->store('providers', 'public');
                }
            }

            $provider = ServiceProvider::create(array_merge([
                'user_id'                        => $user->id,
                'company_name'                   => $request->input('company_name'),
                'company_email'                  => $request->input('company_email'),
                'trade_license'                  => $request->input('trade_license'),
                'license_expiry_date'            => $request->input('license_expiry_date'),
                'business_activity'              => $request->input('business_activity'),
                'company_type'                   => $request->input('company_type'),
                'website'                        => $request->input('website'),
                'primary_contact_name'           => $request->input('primary_contact_name'),
                'primary_contact_mobile'         => $request->input('primary_contact_mobile'),
                'primary_contact_email'          => $request->input('primary_contact_email'),
                'primary_contact_language'       => $request->input('primary_contact_language'),
                'primary_contact_designation'    => $request->input('primary_contact_designation'),
                'secondary_contact_name'         => $request->input('secondary_contact_name'),
                'secondary_contact_mobile'       => $request->input('secondary_contact_mobile'),
                'secondary_contact_email'        => $request->input('secondary_contact_email'),
                'secondary_contact_language'     => $request->input('secondary_contact_language'),
                'secondary_contact_designation'  => $request->input('secondary_contact_designation'),
                'head_office_address'            => $request->input('head_office_address'),
                'emirate_city'                   => $request->input('emirate_city'),
                'google_map_pin'                 => $request->input('google_map_pin'),
                'branch_details'                 => $request->input('branch_details'),
                'working_days'                   => $request->input('working_days'),
                'working_hours_from'             => $request->input('working_hours_from'),
                'working_hours_to'               => $request->input('working_hours_to'),
                'bank_name'                      => $request->input('bank_name'),
                'account_name'                   => $request->input('account_name'),
                'iban_account'                   => $request->input('iban_account'),
                'bank_branch'                    => $request->input('bank_branch'),
                'swift_code'                     => $request->input('swift_code'),
                'comm_mobile'                    => $request->input('comm_mobile'),
                'comm_language'                  => $request->input('comm_language'),
                'emirates_id_doc'                => $request->input('emirates_id_doc'),
                'owner_emirates_id'              => $request->input('owner_emirates_id'),
                'kyc_status'                     => 'no',
                'approval_status'                => 'pending',
                'is_active'                      => false,
                'is_trusted'                     => false,
            ], $fileData));

            $selectedServices = $request->input('selected_services', []);
            $turnaroundTimes  = $request->input('turnaround_times', []);
            $serviceRemarks   = $request->input('service_remarks', []);

            foreach ($selectedServices as $serviceId) {
                $sid = (int) $serviceId;
                $provider->services()->attach($sid, [
                    'turnaround_time' => $turnaroundTimes[$sid] ?? null,
                    'remarks'         => $serviceRemarks[$sid]  ?? null,
                    'is_active'       => true,
                ]);
            }

            DB::commit();

            return redirect()->route('provider-registration.create')
                ->with('success', 'Thank you! Your registration has been submitted successfully. Our team will review your application and get back to you within 2–3 business days.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            throw $e;

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('provider-registration.create')
                ->with('error', 'Registration failed. Please check your details and try again.');
        }
    }
}
