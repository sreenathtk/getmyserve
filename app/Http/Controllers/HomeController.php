<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Offer;
use App\Models\Service;
use App\Models\ServiceProvider;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::where('is_active', true)
            ->with(['subCategories' => function ($q) {
                $q->where('is_active', true)
                    ->with(['services' => function ($q) {
                        $q->where('is_active', true)->orderBy('name');
                    }])->orderBy('name');
            }])
            ->orderBy('name')
            ->get()
            ->filter(fn($c) => $c->subCategories->some(fn($s) => $s->services->isNotEmpty()))
            ->values();

        $offers = Offer::active()
            ->with('services')
            ->orderBy('sort_order')
            ->orderBy('start_date')
            ->get();

        $trustedProviders = ServiceProvider::where('is_trusted', true)
            ->where('is_active', true)
            //->where('approval_status', 'approved')
            ->orderBy('company_name')
            ->get();

        $trendingServices = Service::where('is_trending', true)
            ->where('is_active', true)
            ->select('id', 'name', 'sub_category_id')
            ->orderBy('name')
            ->get();

        return view('welcome', [
            'categories'       => $categories,
            'offers'           => $offers,
            'trustedProviders' => $trustedProviders,
            'trendingServices' => $trendingServices,
            'cartCount'        => count(session('cart', [])),
        ]);
    }

    public function searchServices(Request $request)
    {
        $q = trim($request->query('q', ''));

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $results = Service::where('is_active', true)
            ->where('name', 'like', '%' . $q . '%')
            ->select('id', 'name', 'sub_category_id')
            ->orderByRaw('LOCATE(?, name) ASC', [$q])
            ->orderBy('name')
            ->limit(5)
            ->get();

        return response()->json($results);
    }
}
