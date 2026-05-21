<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServiceReviewController extends Controller
{
    public function show(Service $service)
    {
        abort_unless($service->is_active, 404);

        $reviews = $service->activeReviews()
            ->with('user')
            ->latest()
            ->paginate(8, ['*'], 'review_page');

        return view('services.review', compact('service', 'reviews'));
    }

    public function store(Request $request, Service $service)
    {
        $request->validate([
            'rating'         => 'required|integer|min:1|max:5',
            'comment'        => 'nullable|string|max:10000',
            'attachments.*'  => 'nullable|file|max:5120|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx',
        ]);

        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('review-files', 'public');
                $attachments[] = Storage::url($path);
            }
        }

        ServiceReview::updateOrCreate(
            ['service_id' => $service->id, 'user_id' => $request->user()->id],
            [
                'rating'      => $request->rating,
                'comment'     => $request->comment,
                'attachments' => $attachments ?: null,
                'is_active'   => true,
            ]
        );

        return back()->with('success', 'Your review has been submitted. Thank you!');
    }

    public function uploadImage(Request $request)
    {
        $request->validate(['image' => 'required|image|max:2048']);

        $path = $request->file('image')->store('review-images', 'public');

        return response()->json(['url' => Storage::url($path)]);
    }
}
