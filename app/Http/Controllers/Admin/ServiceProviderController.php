<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use App\Models\Order;
use App\Models\Service;
use App\Models\ServiceProvider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ServiceProviderController extends Controller
{
    public function index()
    {
        $providers = ServiceProvider::with('user', 'services')->orderBy('created_at', 'desc')->get();
        return view('admin.service-providers.index', compact('providers'));
    }

    public function show(Request $request, ServiceProvider $serviceProvider)
    {
        $serviceProvider->load('user', 'services');

        $enquiryQuery = Enquiry::with(['service', 'customer'])
            ->where('assigned_sp_id', $serviceProvider->user_id)
            ->latest();

        if ($request->filled('enq_search')) {
            $s = $request->enq_search;
            $enquiryQuery->where(function ($q) use ($s) {
                $q->where('first_name', 'like', "%{$s}%")
                  ->orWhere('last_name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        if ($request->filled('enq_status')) {
            $enquiryQuery->where('status', $request->enq_status);
        }

        $enquiries = $enquiryQuery->paginate(10, ['*'], 'enq_page')->withQueryString();

        $orderQuery = Order::with(['user', 'orderItems'])
            ->where('assigned_sp_id', $serviceProvider->user_id)
            ->latest();

        if ($request->filled('ord_search')) {
            $s = $request->ord_search;
            $orderQuery->where(function ($q) use ($s) {
                $q->where('id', 'like', "%{$s}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$s}%")
                                                    ->orWhere('email', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('ord_status')) {
            $orderQuery->where('status', $request->ord_status);
        }

        $orders = $orderQuery->paginate(10, ['*'], 'ord_page')->withQueryString();

        return view('admin.service-providers.show', compact('serviceProvider', 'enquiries', 'orders'));
    }

    public function create()
    {
        $services = Service::where('is_active', true)->orderBy('name')->get();
        return view('admin.service-providers.create', compact('services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'primary_contact_name' => 'required|string|max:255',
            'primary_contact_mobile' => 'required|string|max:20',
        ]);

        DB::beginTransaction();
        try {
            // Create user account for the service provider
            $user = User::create([
                'name' => $request->company_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'service_provider',
                'is_active' => true,
            ]);

            // Create service provider record
            $provider = ServiceProvider::create([
                'user_id' => $user->id,
                // Company Profile
                'company_name' => $request->company_name,
                'trade_license' => $request->trade_license,
                'license_expiry_date' => $request->license_expiry_date,
                'business_activity' => $request->business_activity,
                'company_type' => $request->company_type,
                'website' => $request->website,
                'company_email' => $request->company_email,
                // Primary Contact
                'primary_contact_name' => $request->primary_contact_name,
                'primary_contact_mobile' => $request->primary_contact_mobile,
                'primary_contact_email' => $request->primary_contact_email,
                'primary_contact_language' => $request->primary_contact_language,
                'primary_contact_designation' => $request->primary_contact_designation,
                // Secondary Contact
                'secondary_contact_name' => $request->secondary_contact_name,
                'secondary_contact_mobile' => $request->secondary_contact_mobile,
                'secondary_contact_email' => $request->secondary_contact_email,
                'secondary_contact_language' => $request->secondary_contact_language,
                'secondary_contact_designation' => $request->secondary_contact_designation,
                // Location
                'head_office_address' => $request->head_office_address,
                'emirate_city' => $request->emirate_city,
                'google_map_pin' => $request->google_map_pin,
                'branch_details' => $request->branch_details,
                // Business Operations
                'working_days' => $request->working_days,
                'working_hours_from' => $request->working_hours_from,
                'working_hours_to' => $request->working_hours_to,
                // Bank Details
                'bank_name' => $request->bank_name,
                'account_name' => $request->account_name,
                'iban_account' => $request->iban_account,
                'bank_branch' => $request->bank_branch,
                'swift_code' => $request->swift_code,
                // Communication
                'comm_mobile' => $request->comm_mobile,
                'comm_language' => $request->comm_language,
                // Compliances
                'kyc_status' => $request->kyc_status ?? 'no',
                'verified_by' => $request->verified_by,
                'approval_status' => $request->approval_status ?? 'pending',
                'is_trusted' => $request->boolean('is_trusted'),
            ]);

            // Handle file uploads
            $fileFields = [
                'company_logo', 'trade_license_copy', 'emirates_id_doc',
                'passport_copy', 'visa_copy', 'vat_certificate',
                'ejari_contract', 'company_stamp', 'signboard_image',
            ];

            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    $path = $request->file($field)->store('service-providers/' . $provider->id, 'public');
                    $provider->update([$field => $path]);
                }
            }

            // Handle owner_emirates_id (text field, not file)
            if ($request->owner_emirates_id) {
                $provider->update(['owner_emirates_id' => $request->owner_emirates_id]);
            }

            // Attach services
            if ($request->has('services')) {
                foreach ($request->services as $index => $serviceId) {
                    $b2b = $request->b2b_prices[$index] ?? null;
                    $markup = $request->markup_percents[$index] ?? null;
                    $finalPrice = ($b2b !== null && $markup !== null)
                        ? round($b2b * (1 + $markup / 100), 2)
                        : null;

                    $provider->services()->attach($serviceId, [
                        'turnaround_time' => $request->turnaround_times[$index] ?? null,
                        'b2b_price'       => $b2b,
                        'markup_percent'  => $markup,
                        'market_price'    => $request->market_prices[$index] ?? null,
                        'final_price'     => $finalPrice,
                        'remarks'         => $request->service_remarks[$index] ?? null,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.service-providers.index')
                ->with('success', 'Service Provider added successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating service provider: ' . $e->getMessage());
        }
    }

    public function edit(ServiceProvider $serviceProvider)
    {
        $services = Service::where('is_active', true)->orderBy('name')->get();
        $serviceProvider->load('services', 'user');
        return view('admin.service-providers.edit', compact('serviceProvider', 'services'));
    }

    public function update(Request $request, ServiceProvider $serviceProvider)
    {
        $request->validate([
            'company_name'           => 'required|string|max:255',
            'primary_contact_name'   => 'required|string|max:255',
            'primary_contact_mobile' => 'required|string|max:20',
            'email'                  => 'required|email|unique:users,email,' . $serviceProvider->user_id,
            'password'               => 'nullable|string|min:6|confirmed',
        ]);

        DB::beginTransaction();
        try {
            $serviceProvider->update([
                // Company Profile
                'company_name' => $request->company_name,
                'trade_license' => $request->trade_license,
                'license_expiry_date' => $request->license_expiry_date,
                'business_activity' => $request->business_activity,
                'company_type' => $request->company_type,
                'website' => $request->website,
                'company_email' => $request->company_email,
                // Primary Contact
                'primary_contact_name' => $request->primary_contact_name,
                'primary_contact_mobile' => $request->primary_contact_mobile,
                'primary_contact_email' => $request->primary_contact_email,
                'primary_contact_language' => $request->primary_contact_language,
                'primary_contact_designation' => $request->primary_contact_designation,
                // Secondary Contact
                'secondary_contact_name' => $request->secondary_contact_name,
                'secondary_contact_mobile' => $request->secondary_contact_mobile,
                'secondary_contact_email' => $request->secondary_contact_email,
                'secondary_contact_language' => $request->secondary_contact_language,
                'secondary_contact_designation' => $request->secondary_contact_designation,
                // Location
                'head_office_address' => $request->head_office_address,
                'emirate_city' => $request->emirate_city,
                'google_map_pin' => $request->google_map_pin,
                'branch_details' => $request->branch_details,
                // Business Operations
                'working_days' => $request->working_days,
                'working_hours_from' => $request->working_hours_from,
                'working_hours_to' => $request->working_hours_to,
                // Bank Details
                'bank_name' => $request->bank_name,
                'account_name' => $request->account_name,
                'iban_account' => $request->iban_account,
                'bank_branch' => $request->bank_branch,
                'swift_code' => $request->swift_code,
                // Communication
                'comm_mobile' => $request->comm_mobile,
                'comm_language' => $request->comm_language,
                // Compliances
                'kyc_status' => $request->kyc_status ?? $serviceProvider->kyc_status,
                'verified_by' => $request->verified_by,
                'approval_status' => $request->approval_status ?? $serviceProvider->approval_status,
                'is_trusted' => $request->boolean('is_trusted'),
            ]);

            // Handle file uploads
            $fileFields = [
                'company_logo', 'trade_license_copy', 'emirates_id_doc',
                'passport_copy', 'visa_copy', 'vat_certificate',
                'ejari_contract', 'company_stamp', 'signboard_image',
            ];

            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    $path = $request->file($field)->store('service-providers/' . $serviceProvider->id, 'public');
                    $serviceProvider->update([$field => $path]);
                }
            }

            if ($request->owner_emirates_id) {
                $serviceProvider->update(['owner_emirates_id' => $request->owner_emirates_id]);
            }

            // Update user login credentials
            $userUpdate = [
                'name'  => $request->company_name,
                'email' => $request->email,
            ];
            if ($request->filled('password')) {
                $userUpdate['password'] = Hash::make($request->password);
            }
            $serviceProvider->user->update($userUpdate);

            // Sync services
            $serviceProvider->services()->detach();
            if ($request->has('services')) {
                foreach ($request->services as $index => $serviceId) {
                    $b2b = $request->b2b_prices[$index] ?? null;
                    $markup = $request->markup_percents[$index] ?? null;
                    $finalPrice = ($b2b !== null && $markup !== null)
                        ? round($b2b * (1 + $markup / 100), 2)
                        : null;

                    $serviceProvider->services()->attach($serviceId, [
                        'turnaround_time' => $request->turnaround_times[$index] ?? null,
                        'b2b_price'       => $b2b,
                        'markup_percent'  => $markup,
                        'market_price'    => $request->market_prices[$index] ?? null,
                        'final_price'     => $finalPrice,
                        'remarks'         => $request->service_remarks[$index] ?? null,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.service-providers.index')
                ->with('success', 'Service Provider updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating service provider: ' . $e->getMessage());
        }
    }

    public function toggleStatus(ServiceProvider $serviceProvider)
    {
        $serviceProvider->update(['is_active' => !$serviceProvider->is_active]);
        $serviceProvider->user->update(['is_active' => !$serviceProvider->user->is_active]);

        $status = $serviceProvider->is_active ? 'enabled' : 'disabled';
        return redirect()->route('admin.service-providers.index')
            ->with('success', "Service Provider {$status} successfully.");
    }

    public function toggleTrusted(ServiceProvider $serviceProvider)
    {
        $serviceProvider->update(['is_trusted' => !$serviceProvider->is_trusted]);

        $status = $serviceProvider->is_trusted ? 'marked as trusted' : 'removed from trusted';
        return back()->with('success', "Service Provider {$status} successfully.");
    }
}
