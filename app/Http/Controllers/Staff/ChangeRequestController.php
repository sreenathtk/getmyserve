<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceProvider;
use App\Models\ServiceProviderChangeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChangeRequestController extends Controller
{
    public function index()
    {
        $requests = ServiceProviderChangeRequest::with('serviceProvider', 'reviewedBy')
            ->where('requested_by_user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('staff.change-requests.index', compact('requests'));
    }

    public function create(Request $request)
    {
        $services = Service::where('is_active', true)->orderBy('name')->get();
        $providers = ServiceProvider::orderBy('company_name')->get();
        $requestType = $request->query('type', 'create');
        $serviceProvider = null;

        if ($requestType === 'update' && $request->query('provider')) {
            $serviceProvider = ServiceProvider::with('services')->findOrFail($request->query('provider'));
        }

        return view('staff.change-requests.create', compact('services', 'providers', 'requestType', 'serviceProvider'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'request_type' => 'required|in:create,update',
            'company_name' => 'required|string|max:255',
            'primary_contact_name' => 'required|string|max:255',
            'primary_contact_mobile' => 'required|string|max:20',
        ]);

        if ($request->request_type === 'create') {
            $request->validate([
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
            ]);
        }

        if ($request->request_type === 'update') {
            $request->validate([
                'service_provider_id' => 'required|exists:service_providers,id',
            ]);
        }

        $payload = $this->buildPayload($request);

        $changeRequest = ServiceProviderChangeRequest::create([
            'requested_by_user_id' => Auth::id(),
            'request_type' => $request->request_type,
            'service_provider_id' => $request->request_type === 'update' ? $request->service_provider_id : null,
            'payload' => $payload,
            'status' => 'pending',
        ]);

        $documents = $this->handleFileUploads($request, $changeRequest->id);
        if (!empty($documents)) {
            $payload['documents'] = array_merge($payload['documents'] ?? [], $documents);
            $changeRequest->update(['payload' => $payload]);
        }

        return redirect()->route('staff.change-requests.index')
            ->with('success', 'Change request submitted successfully. Awaiting admin approval.');
    }

    public function edit(ServiceProviderChangeRequest $changeRequest)
    {
        if ($changeRequest->requested_by_user_id !== Auth::id() || !$changeRequest->isPending()) {
            abort(403, 'You cannot edit this request.');
        }

        $services = Service::where('is_active', true)->orderBy('name')->get();
        $providers = ServiceProvider::orderBy('company_name')->get();

        return view('staff.change-requests.edit', compact('changeRequest', 'services', 'providers'));
    }

    public function update(Request $request, ServiceProviderChangeRequest $changeRequest)
    {
        if ($changeRequest->requested_by_user_id !== Auth::id() || !$changeRequest->isPending()) {
            abort(403, 'You cannot edit this request.');
        }

        $request->validate([
            'company_name' => 'required|string|max:255',
            'primary_contact_name' => 'required|string|max:255',
            'primary_contact_mobile' => 'required|string|max:20',
        ]);

        $payload = $this->buildPayload($request);

        $existingDocs = $changeRequest->payload['documents'] ?? [];
        $newDocs = $this->handleFileUploads($request, $changeRequest->id);
        $payload['documents'] = array_merge($existingDocs, $newDocs);

        $changeRequest->update(['payload' => $payload]);

        return redirect()->route('staff.change-requests.index')
            ->with('success', 'Change request updated successfully.');
    }

    private function buildPayload(Request $request): array
    {
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
                'kyc_status' => $request->kyc_status ?? 'no',
                'verified_by' => $request->verified_by,
                'emirates_id_doc' => $request->emirates_id_doc,
                'owner_emirates_id' => $request->owner_emirates_id,
            ],
            'services' => [],
            'documents' => [],
        ];

        if ($request->request_type === 'create') {
            $payload['user'] = [
                'email' => $request->email,
                'password' => $request->password,
            ];
        }

        if ($request->has('services')) {
            foreach ($request->services as $index => $serviceId) {
                if ($serviceId) {
                    $b2b = $request->b2b_prices[$index] ?? null;
                    $markup = $request->markup_percents[$index] ?? null;
                    $finalPrice = ($b2b !== null && $markup !== null)
                        ? round($b2b * (1 + $markup / 100), 2)
                        : null;

                    $payload['services'][] = [
                        'service_id'      => $serviceId,
                        'turnaround_time' => $request->turnaround_times[$index] ?? null,
                        'b2b_price'       => $b2b,
                        'markup_percent'  => $markup,
                        'market_price'    => $request->market_prices[$index] ?? null,
                        'final_price'     => $finalPrice,
                        'remarks'         => $request->service_remarks[$index] ?? null,
                    ];
                }
            }
        }

        return $payload;
    }

    private function handleFileUploads(Request $request, int $requestId): array
    {
        $documents = [];
        $fileFields = [
            'company_logo', 'trade_license_copy', 'passport_copy',
            'visa_copy', 'vat_certificate', 'ejari_contract',
            'company_stamp', 'signboard_image',
        ];

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $path = $request->file($field)->store('change-requests/' . $requestId, 'public');
                $documents[$field] = $path;
            }
        }

        return $documents;
    }
}
