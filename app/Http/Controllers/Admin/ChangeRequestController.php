<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceProvider;
use App\Models\ServiceProviderChangeRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ChangeRequestController extends Controller
{
    public function index()
    {
        $requests = ServiceProviderChangeRequest::with('requestedBy', 'serviceProvider', 'reviewedBy')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.change-requests.index', compact('requests'));
    }

    public function show(ServiceProviderChangeRequest $changeRequest)
    {
        $changeRequest->load('requestedBy', 'serviceProvider.services', 'reviewedBy');
        $services = Service::where('is_active', true)->orderBy('name')->get();

        return view('admin.change-requests.show', compact('changeRequest', 'services'));
    }

    public function approve(ServiceProviderChangeRequest $changeRequest)
    {
        if (!$changeRequest->isPending()) {
            return back()->with('error', 'This request has already been reviewed.');
        }

        DB::beginTransaction();
        try {
            $payload = $changeRequest->payload;

            if ($changeRequest->request_type === 'create') {
                $this->applyCreateRequest($changeRequest, $payload);
            } else {
                $this->applyUpdateRequest($changeRequest, $payload);
            }

            $changeRequest->update([
                'status' => 'approved',
                'reviewed_by_user_id' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('admin.change-requests.index')
                ->with('success', 'Change request approved and changes applied.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error applying change request: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, ServiceProviderChangeRequest $changeRequest)
    {
        if (!$changeRequest->isPending()) {
            return back()->with('error', 'This request has already been reviewed.');
        }

        $request->validate(['rejection_reason' => 'nullable|string|max:1000']);

        $changeRequest->update([
            'status' => 'rejected',
            'reviewed_by_user_id' => Auth::id(),
            'reviewed_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return redirect()->route('admin.change-requests.index')
            ->with('success', 'Change request rejected.');
    }

    private function applyCreateRequest(ServiceProviderChangeRequest $changeRequest, array $payload): void
    {
        $user = User::create([
            'name' => $payload['provider']['company_name'],
            'email' => $payload['user']['email'],
            'password' => Hash::make($payload['user']['password']),
            'role' => 'service_provider',
            'is_active' => true,
        ]);

        $providerFields = $payload['provider'];
        $providerFields['user_id'] = $user->id;

        $provider = ServiceProvider::create($providerFields);

        if (!empty($payload['documents'])) {
            foreach ($payload['documents'] as $field => $path) {
                $provider->update([$field => $path]);
            }
        }

        foreach ($payload['services'] ?? [] as $service) {
            if (!empty($service['service_id'])) {
                $provider->services()->attach($service['service_id'], [
                    'turnaround_time' => $service['turnaround_time'] ?? null,
                    'b2b_price'       => $service['b2b_price'] ?? null,
                    'markup_percent'  => $service['markup_percent'] ?? null,
                    'market_price'    => $service['market_price'] ?? null,
                    'final_price'     => $service['final_price'] ?? null,
                    'remarks'         => $service['remarks'] ?? null,
                ]);
            }
        }

        // Link the newly created provider back to the change request record
        $changeRequest->service_provider_id = $provider->id;
    }

    private function applyUpdateRequest(ServiceProviderChangeRequest $changeRequest, array $payload): void
    {
        $provider = ServiceProvider::findOrFail($changeRequest->service_provider_id);

        $provider->update($payload['provider']);

        if (!empty($payload['documents'])) {
            foreach ($payload['documents'] as $field => $path) {
                $provider->update([$field => $path]);
            }
        }

        $provider->user->update(['name' => $payload['provider']['company_name']]);

        $provider->services()->detach();
        foreach ($payload['services'] ?? [] as $service) {
            if (!empty($service['service_id'])) {
                $provider->services()->attach($service['service_id'], [
                    'turnaround_time' => $service['turnaround_time'] ?? null,
                    'b2b_price'       => $service['b2b_price'] ?? null,
                    'markup_percent'  => $service['markup_percent'] ?? null,
                    'market_price'    => $service['market_price'] ?? null,
                    'final_price'     => $service['final_price'] ?? null,
                    'remarks'         => $service['remarks'] ?? null,
                ]);
            }
        }
    }
}
