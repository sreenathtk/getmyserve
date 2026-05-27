<?php

namespace App\Http\Controllers;

use App\Models\AssistanceRequest;
use App\Models\Enquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show()
    {
        $user               = auth()->user();
        $orders             = $user->orders()->latest()->paginate(5);
        $enquiries          = Enquiry::where('user_id', $user->id)->with('service')->latest()->get();
        $assistanceRequests = AssistanceRequest::where('user_id', $user->id)->with('service')->latest()->get();

        return view('profile.index', compact('user', 'orders', 'enquiries', 'assistanceRequests'));
    }

    public function enquiryDetail(Enquiry $enquiry)
    {
        abort_unless($enquiry->user_id === auth()->id(), 403);
        $enquiry->load('service.subCategory.category');
        return view('profile.enquiry-detail', compact('enquiry'));
    }

    public function assistanceDetail(AssistanceRequest $assistanceRequest)
    {
        abort_unless($assistanceRequest->user_id === auth()->id(), 403);
        $assistanceRequest->load('service');
        return view('profile.assistance-detail', compact('assistanceRequest'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'                   => 'required|string|max:255',
            'email'                  => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone'                  => 'nullable|string|max:20',
            'whatsapp_number'        => 'nullable|string|max:20',
            'country'                => 'nullable|string|max:100',
            'city'                   => 'nullable|string|max:100',
            'address'                => 'nullable|string|max:500',
            'whatsapp_notifications' => 'nullable|boolean',
        ]);

        $user->update(array_merge(
            $request->only('name', 'email', 'phone', 'whatsapp_number', 'country', 'city', 'address'),
            ['whatsapp_notifications' => $request->boolean('whatsapp_notifications')],
        ));

        return back()->with('profile_success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.'])->with('tab', 'password');
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('password_success', 'Password updated successfully.')->with('tab', 'password');
    }
}
