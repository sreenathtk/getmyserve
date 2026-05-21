<?php

namespace App\Http\Controllers;

use App\Models\AssistanceRequest;
use App\Models\Service;
use Illuminate\Http\Request;

class AssistanceRequestController extends Controller
{
    public function create()
    {
        $services = Service::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('assistance-request', compact('services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|max:255',
            'phone'      => 'required|string|max:50',
            'whatsapp'   => 'nullable|string|max:50',
            'service_id' => 'nullable|integer|exists:services,id',
            'location'   => 'nullable|string|max:255',
            'language'   => 'nullable|string|max:100',
            'remarks'    => 'nullable|string|max:2000',
        ]);

        AssistanceRequest::create(array_merge(
            $request->only([
                'first_name', 'last_name', 'email', 'phone',
                'whatsapp', 'service_id', 'location', 'language', 'remarks',
            ]),
            ['user_id' => auth()->id()]
        ));

        return redirect()->route('assistance-request.create')
            ->with('success', 'Thank you! Your request has been submitted. We will contact you within 24 hours.');
    }
}
