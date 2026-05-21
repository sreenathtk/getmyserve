<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceReview;

class ServiceReviewController extends Controller
{
    public function index(Service $service)
    {
        $reviews = $service->reviews()
            ->with('user')
            ->latest()
            ->get();

        // Rating distribution (1–5) counting only active reviews
        $distribution = [];
        for ($i = 5; $i >= 1; $i--) {
            $distribution[$i] = $service->reviews()
                ->where('rating', $i)
                ->where('is_active', true)
                ->count();
        }

        $activeCount = $reviews->where('is_active', true)->count();
        $avgRating   = $activeCount > 0
            ? round($reviews->where('is_active', true)->avg('rating'), 1)
            : null;

        return view('admin.services.reviews', compact(
            'service', 'reviews', 'distribution', 'activeCount', 'avgRating'
        ));
    }

    public function toggle(Service $service, ServiceReview $review)
    {
        abort_unless($review->service_id === $service->id, 404);

        $review->update(['is_active' => !$review->is_active]);

        $state = $review->is_active ? 'enabled' : 'disabled';
        return back()->with('success', "Review {$state} successfully.");
    }
}
