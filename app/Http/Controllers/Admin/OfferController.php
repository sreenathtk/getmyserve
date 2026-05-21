<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Offer;
use App\Models\Service;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function index()
    {
        $offers = Offer::with('services')
            ->orderBy('sort_order')
            ->orderBy('start_date')
            ->get();

        return view('admin.offers.index', compact('offers'));
    }

    public function create()
    {
        $categories    = Category::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $subCategories = SubCategory::where('is_active', true)->orderBy('name')->get(['id', 'category_id', 'name']);
        $services      = Service::where('is_active', true)->orderBy('name')->get(['id', 'sub_category_id', 'name', 'has_premium_package']);
        $offerTypes    = Offer::$offerTypeLabels;

        // Restore service row selections after a validation failure
        $oldServiceIds    = old('service_ids', []);
        $oldServicePkgs   = old('service_packages', []);
        $selectedServices = [];

        if (!empty($oldServiceIds)) {
            $svcMap    = $services->keyBy('id');
            $subCatMap = $subCategories->keyBy('id');

            foreach ($oldServiceIds as $index => $svcId) {
                $svc    = $svcMap->get($svcId);
                $subCat = $svc ? $subCatMap->get($svc->sub_category_id) : null;

                $selectedServices[] = [
                    'service_id'      => (string) $svcId,
                    'sub_category_id' => $svc ? (string) $svc->sub_category_id : '',
                    'category_id'     => $subCat ? (string) $subCat->category_id : '',
                    'package'         => $oldServicePkgs[$index] ?? '',
                ];
            }
        }

        if (empty($selectedServices)) {
            $selectedServices = [['service_id' => '', 'sub_category_id' => '', 'category_id' => '', 'package' => '']];
        }

        return view('admin.offers.create', compact('categories', 'subCategories', 'services', 'offerTypes', 'selectedServices'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'service_ids'      => 'required|array|min:1',
            'service_ids.*'    => 'required|exists:services,id',
            'service_packages' => 'present|array',
            'service_packages.*' => 'present',
            'offer_type'       => 'required|in:' . implode(',', array_keys(Offer::$offerTypeLabels)),
            'title'            => 'required|string|max:255',
            'offer_price'      => 'required|numeric|min:0',
            'offer_detail'     => 'nullable|string',
            'button_text'      => 'required|string|max:100',
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after_or_equal:start_date',
            'sort_order'       => 'nullable|integer|min:0',
        ]);

        $serviceIds      = $data['service_ids'];
        $servicePackages = $data['service_packages'] ?? [];
        unset($data['service_ids'], $data['service_packages']);

        $syncData = [];
        foreach ($serviceIds as $i => $svcId) {
            $pkg = in_array($servicePackages[$i] ?? '', ['basic', 'premium']) ? $servicePackages[$i] : null;
            $syncData[(int) $svcId] = ['package' => $pkg];
        }

        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active']   = true;
        $data['sort_order']  = $data['sort_order'] ?? 0;

        $offer = Offer::create($data);
        $offer->services()->sync($syncData);

        return redirect()->route('admin.offers.index')
            ->with('success', 'Offer created successfully.');
    }

    public function edit(Offer $offer)
    {
        $offer->load('services.subCategory.category');

        $categories    = Category::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $subCategories = SubCategory::where('is_active', true)->orderBy('name')->get(['id', 'category_id', 'name']);
        $services      = Service::where('is_active', true)->orderBy('name')->get(['id', 'sub_category_id', 'name', 'has_premium_package']);
        $offerTypes    = Offer::$offerTypeLabels;

        $selectedServices = $offer->services->map(fn($svc) => [
            'service_id'      => (string) $svc->id,
            'sub_category_id' => (string) ($svc->sub_category_id ?? ''),
            'category_id'     => (string) ($svc->subCategory?->category_id ?? ''),
            'package'         => $svc->pivot->package ?? '',
        ])->values()->all();

        if (empty($selectedServices)) {
            $selectedServices = [['service_id' => '', 'sub_category_id' => '', 'category_id' => '', 'package' => '']];
        }

        // After a validation failure, restore old input over the saved data
        $oldServiceIds = old('service_ids');
        if ($oldServiceIds !== null) {
            $oldServicePkgs   = old('service_packages', []);
            $svcMap    = $services->keyBy('id');
            $subCatMap = $subCategories->keyBy('id');
            $selectedServices = [];

            foreach ($oldServiceIds as $index => $svcId) {
                $svc    = $svcMap->get($svcId);
                $subCat = $svc ? $subCatMap->get($svc->sub_category_id) : null;

                $selectedServices[] = [
                    'service_id'      => (string) $svcId,
                    'sub_category_id' => $svc ? (string) $svc->sub_category_id : '',
                    'category_id'     => $subCat ? (string) $subCat->category_id : '',
                    'package'         => $oldServicePkgs[$index] ?? '',
                ];
            }

            if (empty($selectedServices)) {
                $selectedServices = [['service_id' => '', 'sub_category_id' => '', 'category_id' => '', 'package' => '']];
            }
        }

        return view('admin.offers.edit', compact(
            'offer', 'categories', 'subCategories', 'services', 'offerTypes', 'selectedServices'
        ));
    }

    public function update(Request $request, Offer $offer)
    {
        $data = $request->validate([
            'service_ids'        => 'required|array|min:1',
            'service_ids.*'      => 'required|exists:services,id',
            'service_packages'   => 'present|array',
            'service_packages.*' => 'present',
            'offer_type'         => 'required|in:' . implode(',', array_keys(Offer::$offerTypeLabels)),
            'title'              => 'required|string|max:255',
            'offer_price'        => 'required|numeric|min:0',
            'offer_detail'       => 'nullable|string',
            'button_text'        => 'required|string|max:100',
            'start_date'         => 'required|date',
            'end_date'           => 'required|date|after_or_equal:start_date',
            'sort_order'         => 'nullable|integer|min:0',
        ]);

        $serviceIds      = $data['service_ids'];
        $servicePackages = $data['service_packages'] ?? [];
        unset($data['service_ids'], $data['service_packages']);

        $syncData = [];
        foreach ($serviceIds as $i => $svcId) {
            $pkg = in_array($servicePackages[$i] ?? '', ['basic', 'premium']) ? $servicePackages[$i] : null;
            $syncData[(int) $svcId] = ['package' => $pkg];
        }

        $data['is_featured'] = $request->boolean('is_featured');
        $data['sort_order']  = $data['sort_order'] ?? 0;

        $offer->update($data);
        $offer->services()->sync($syncData);

        return redirect()->route('admin.offers.index')
            ->with('success', 'Offer updated successfully.');
    }

    public function toggleStatus(Offer $offer)
    {
        $offer->update(['is_active' => !$offer->is_active]);

        $status = $offer->is_active ? 'enabled' : 'disabled';
        return redirect()->route('admin.offers.index')
            ->with('success', "Offer {$status} successfully.");
    }
}
