<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Service;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::with('subCategory.category')
            ->withCount(['reviews', 'activeReviews'])
            ->withAvg('activeReviews', 'rating')
            ->orderBy('name');

        if ($request->filled('sub_category')) {
            $query->where('sub_category_id', $request->sub_category);
        } elseif ($request->filled('category')) {
            $query->whereHas('subCategory', function ($q) use ($request) {
                $q->where('category_id', $request->category);
            });
        }

        $services    = $query->get();
        $categories  = Category::where('is_active', true)->orderBy('name')->get();

        // Pre-load sub-categories for the selected category so the filter can render correctly
        $subCategories = collect();
        if ($request->filled('category')) {
            $subCategories = SubCategory::where('category_id', $request->category)
                ->where('is_active', true)->orderBy('name')->get();
        } elseif ($request->filled('sub_category')) {
            $sub = SubCategory::find($request->sub_category);
            if ($sub) {
                $subCategories = SubCategory::where('category_id', $sub->category_id)
                    ->where('is_active', true)->orderBy('name')->get();
            }
        }

        return view('admin.services.index', compact('services', 'categories', 'subCategories'));
    }

    public function create()
    {
        $categories    = Category::where('is_active', true)->orderBy('name')->get();
        $trendingCount = Service::where('is_trending', true)->count();
        return view('admin.services.create', compact('categories', 'trendingCount'));
    }

    public function store(Request $request)
    {
        $hasPackages    = $request->boolean('has_packages');
        $hasPremium     = $hasPackages && $request->boolean('has_premium_package');
        $basicHasOffer  = $hasPackages && $request->boolean('basic_has_offer');
        $premiumHasOffer = $hasPremium && $request->boolean('premium_has_offer');

        $request->validate([
            'sub_category_id'               => 'nullable|exists:sub_categories,id',
            'name'                          => 'required|string|max:255|unique:services,name',
            'description'                   => 'nullable|string',
            'image'                         => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'icon'                          => 'nullable|string|max:100',
            'service_type'                  => 'required|in:book_now,enquire_now,both',
            'estimate_value'                => 'nullable|integer|min:1',
            'estimate_unit'                 => 'nullable|in:hours,days,weeks|required_with:estimate_value',
            'basic_package_description'     => 'nullable|string',
            'basic_package_price'           => 'nullable|numeric|min:0',
            'basic_package_actual_price'    => $basicHasOffer ? 'required|numeric|min:0' : 'nullable|numeric|min:0',
            'basic_package_discount_type'   => $basicHasOffer ? 'required|in:percentage,flat' : 'nullable|in:percentage,flat',
            'basic_package_discount_value'  => $basicHasOffer ? 'required|numeric|min:0' : 'nullable|numeric|min:0',
            'premium_package_description'   => 'nullable|string',
            'premium_package_price'         => 'nullable|numeric|min:0',
            'premium_package_actual_price'  => $premiumHasOffer ? 'required|numeric|min:0' : 'nullable|numeric|min:0',
            'premium_package_discount_type' => $premiumHasOffer ? 'required|in:percentage,flat' : 'nullable|in:percentage,flat',
            'premium_package_discount_value'=> $premiumHasOffer ? 'required|numeric|min:0' : 'nullable|numeric|min:0',
        ]);

        [$basicPrice, $basicActualPrice, $basicDiscountType, $basicDiscountValue] =
            $this->resolvePackagePricing($request, 'basic', $basicHasOffer);

        [$premiumPrice, $premiumActualPrice, $premiumDiscountType, $premiumDiscountValue] =
            $this->resolvePackagePricing($request, 'premium', $premiumHasOffer);

        $wantsTrending = $request->boolean('is_trending');
        if ($wantsTrending && Service::where('is_trending', true)->count() >= 5) {
            return back()->withErrors(['is_trending' => 'Maximum 5 trending services are allowed at a time.'])->withInput();
        }

        $data = [
            'sub_category_id'               => $request->sub_category_id ?: null,
            'name'                          => $request->name,
            'description'                   => $request->description,
            'icon'                          => $request->icon ?: null,
            'service_type'                  => $request->service_type,
            'is_active'                     => true,
            'is_trending'                   => $wantsTrending,
            'estimate_value'                => $request->estimate_value ?: null,
            'estimate_unit'                 => $request->estimate_value ? $request->estimate_unit : null,
            'has_packages'                  => $hasPackages,
            'has_premium_package'           => $hasPremium,
            'basic_package_description'     => $hasPackages ? $request->basic_package_description : null,
            'basic_package_price'           => $hasPackages ? $basicPrice : null,
            'basic_package_actual_price'    => $hasPackages ? $basicActualPrice : null,
            'basic_package_discount_type'   => $hasPackages ? $basicDiscountType : null,
            'basic_package_discount_value'  => $hasPackages ? $basicDiscountValue : null,
            'premium_package_description'   => $hasPremium ? $request->premium_package_description : null,
            'premium_package_price'         => $hasPremium ? $premiumPrice : null,
            'premium_package_actual_price'  => $hasPremium ? $premiumActualPrice : null,
            'premium_package_discount_type' => $hasPremium ? $premiumDiscountType : null,
            'premium_package_discount_value'=> $hasPremium ? $premiumDiscountValue : null,
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('images/services', 'public');
        }

        Service::create($data);

        return redirect()->route('admin.services.index')
            ->with('success', 'Service created successfully.');
    }

    public function edit(Service $service)
    {
        $categories    = Category::where('is_active', true)->orderBy('name')->get();
        $subCategories = $service->sub_category_id
            ? SubCategory::where('category_id', $service->subCategory->category_id)
                ->where('is_active', true)->orderBy('name')->get()
            : collect();
        $trendingCount = Service::where('is_trending', true)->count();
        return view('admin.services.edit', compact('service', 'categories', 'subCategories', 'trendingCount'));
    }

    public function update(Request $request, Service $service)
    {
        $hasPackages    = $request->boolean('has_packages');
        $hasPremium     = $hasPackages && $request->boolean('has_premium_package');
        $basicHasOffer  = $hasPackages && $request->boolean('basic_has_offer');
        $premiumHasOffer = $hasPremium && $request->boolean('premium_has_offer');

        $request->validate([
            'sub_category_id'               => 'nullable|exists:sub_categories,id',
            'name'                          => 'required|string|max:255|unique:services,name,' . $service->id,
            'description'                   => 'nullable|string',
            'image'                         => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'icon'                          => 'nullable|string|max:100',
            'service_type'                  => 'required|in:book_now,enquire_now,both',
            'estimate_value'                => 'nullable|integer|min:1',
            'estimate_unit'                 => 'nullable|in:hours,days,weeks|required_with:estimate_value',
            'basic_package_description'     => 'nullable|string',
            'basic_package_price'           => 'nullable|numeric|min:0',
            'basic_package_actual_price'    => $basicHasOffer ? 'required|numeric|min:0' : 'nullable|numeric|min:0',
            'basic_package_discount_type'   => $basicHasOffer ? 'required|in:percentage,flat' : 'nullable|in:percentage,flat',
            'basic_package_discount_value'  => $basicHasOffer ? 'required|numeric|min:0' : 'nullable|numeric|min:0',
            'premium_package_description'   => 'nullable|string',
            'premium_package_price'         => 'nullable|numeric|min:0',
            'premium_package_actual_price'  => $premiumHasOffer ? 'required|numeric|min:0' : 'nullable|numeric|min:0',
            'premium_package_discount_type' => $premiumHasOffer ? 'required|in:percentage,flat' : 'nullable|in:percentage,flat',
            'premium_package_discount_value'=> $premiumHasOffer ? 'required|numeric|min:0' : 'nullable|numeric|min:0',
        ]);

        [$basicPrice, $basicActualPrice, $basicDiscountType, $basicDiscountValue] =
            $this->resolvePackagePricing($request, 'basic', $basicHasOffer);

        [$premiumPrice, $premiumActualPrice, $premiumDiscountType, $premiumDiscountValue] =
            $this->resolvePackagePricing($request, 'premium', $premiumHasOffer);

        $wantsTrending = $request->boolean('is_trending');
        if ($wantsTrending && !$service->is_trending && Service::where('is_trending', true)->count() >= 5) {
            return back()->withErrors(['is_trending' => 'Maximum 5 trending services are allowed at a time.'])->withInput();
        }

        $data = [
            'sub_category_id'               => $request->sub_category_id ?: null,
            'name'                          => $request->name,
            'description'                   => $request->description,
            'icon'                          => $request->icon ?: null,
            'service_type'                  => $request->service_type,
            'is_trending'                   => $wantsTrending,
            'estimate_value'                => $request->estimate_value ?: null,
            'estimate_unit'                 => $request->estimate_value ? $request->estimate_unit : null,
            'has_packages'                  => $hasPackages,
            'has_premium_package'           => $hasPremium,
            'basic_package_description'     => $hasPackages ? $request->basic_package_description : null,
            'basic_package_price'           => $hasPackages ? $basicPrice : null,
            'basic_package_actual_price'    => $hasPackages ? $basicActualPrice : null,
            'basic_package_discount_type'   => $hasPackages ? $basicDiscountType : null,
            'basic_package_discount_value'  => $hasPackages ? $basicDiscountValue : null,
            'premium_package_description'   => $hasPremium ? $request->premium_package_description : null,
            'premium_package_price'         => $hasPremium ? $premiumPrice : null,
            'premium_package_actual_price'  => $hasPremium ? $premiumActualPrice : null,
            'premium_package_discount_type' => $hasPremium ? $premiumDiscountType : null,
            'premium_package_discount_value'=> $hasPremium ? $premiumDiscountValue : null,
        ];

        if ($request->hasFile('image')) {
            if ($service->image) {
                Storage::disk('public')->delete($service->image);
            }
            $data['image'] = $request->file('image')->store('images/services', 'public');
        } elseif ($request->boolean('remove_image') && $service->image) {
            Storage::disk('public')->delete($service->image);
            $data['image'] = null;
        }

        $service->update($data);

        return redirect()->route('admin.services.index')
            ->with('success', 'Service updated successfully.');
    }

    public function toggleStatus(Service $service)
    {
        $service->update(['is_active' => !$service->is_active]);

        $status = $service->is_active ? 'enabled' : 'disabled';
        return redirect()->route('admin.services.index')
            ->with('success', "Service {$status} successfully.");
    }

    public function getSubCategories(Category $category)
    {
        $subCategories = $category->subCategories()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);
        return response()->json($subCategories);
    }

    /**
     * Returns [finalPrice, actualPrice, discountType, discountValue] for a package.
     * When offer is active, final price is computed from actual price + discount.
     * When no offer, final price is taken directly from the request.
     */
    private function resolvePackagePricing(Request $request, string $pkg, bool $hasOffer): array
    {
        if ($hasOffer) {
            $actualPrice   = (float) $request->input("{$pkg}_package_actual_price");
            $discountType  = $request->input("{$pkg}_package_discount_type");
            $discountValue = (float) $request->input("{$pkg}_package_discount_value");
            $finalPrice    = $this->computeOfferPrice($actualPrice, $discountType, $discountValue);
            return [$finalPrice, $actualPrice, $discountType, $discountValue];
        }

        return [
            $request->input("{$pkg}_package_price"),
            null,
            null,
            null,
        ];
    }

    private function computeOfferPrice(float $actualPrice, string $discountType, float $discountValue): float
    {
        if ($discountType === 'percentage') {
            return max(0, round($actualPrice - ($actualPrice * $discountValue / 100), 2));
        }
        return max(0, round($actualPrice - $discountValue, 2));
    }
}
