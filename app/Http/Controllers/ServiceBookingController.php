<?php

namespace App\Http\Controllers;

use App\Events\Customer\EnquirySubmitted;
use App\Events\Provider\ProviderNewServiceEnquiry;
use App\Models\Enquiry;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceBookingController extends Controller
{
    public function create(Service $service)
    {
        abort_unless($service->is_active, 404);

        $service->load('subCategory.category');

        $addonServices = collect();
        if ($service->sub_category_id) {
            $addonServices = Service::where('sub_category_id', $service->sub_category_id)
                ->where('id', '!=', $service->getKey())
                ->where('is_active', true)
                ->whereIn('service_type', ['book_now', 'both'])
                ->orderBy('name')
                ->get();
        }

        return view('bookservice', compact('service', 'addonServices'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|integer|exists:services,id',
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|max:255',
            'phone'      => 'required|string|max:50',
            'whatsapp'   => 'nullable|string|max:50',
            'location'   => 'nullable|string|max:255',
            'language'   => 'nullable|string|max:100',
            'remarks'    => 'nullable|string|max:2000',
        ]);

        $enquiry = Enquiry::create(array_merge(
            $request->only([
                'service_id', 'first_name', 'last_name', 'email',
                'phone', 'whatsapp', 'location', 'language', 'remarks',
            ]),
            ['user_id' => auth()->id()]
        ));

        event(new EnquirySubmitted($enquiry));
        event(new ProviderNewServiceEnquiry($enquiry));

        return redirect()->route('home')
            ->with('enquiry_success', 'Thank you! Your enquiry has been submitted. We will contact you shortly.');
    }
}
