<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Offer;
use App\Models\Service;
use App\Models\Setting;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private function cartQuery()
    {
        return auth()->check()
            ? CartItem::where('user_id', auth()->id())
            : CartItem::where('session_id', session()->getId());
    }

    public function index()
    {
        $cartItems = $this->cartQuery()->latest()->get();

        $comboOfferIds = $cartItems->pluck('combo_offer_id')->filter()->unique();
        $comboOffers   = $comboOfferIds->isNotEmpty()
            ? Offer::whereIn('id', $comboOfferIds)->get()->keyBy('id')
            : collect();

        $vatRate = (float) Setting::get('vat_rate', '0');

        return view('cart', compact('cartItems', 'comboOffers', 'vatRate'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'service_id' => 'required|integer|exists:services,id',
            'package'    => 'required|in:basic,premium,standard',
        ]);

        $service = Service::where('is_active', true)->findOrFail($request->service_id);
        $package = $request->package;

        $originalPrice = null;
        $offerPrice = null;
        if ($service->has_packages) {
            $originalPrice = $package === 'premium'
                ? $service->premium_package_actual_price
                : $service->basic_package_actual_price;
            $offerPrice = $package === 'premium'
                ? $service->premium_package_price
                : $service->basic_package_price;
        }
 
        $lookup = auth()->check()
            ? ['user_id' => auth()->id(), 'service_id' => $service->id]
            : ['session_id' => session()->getId(), 'service_id' => $service->id];

        CartItem::updateOrCreate($lookup, [
            'package'         => $package,
            'price'           => $offerPrice,
            'original_price'  => $originalPrice,
            'combo_offer_id'  => null,
            'combo_group'     => null,
            'service_name'    => $service->name,
            'image'           => $service->image,
        ]);

        return redirect()->back()->with('cart_success', "{$service->name} added to cart.");
    }

    public function addOffer(Request $request)
    {
        $request->validate(['offer_id' => 'required|integer|exists:offers,id']);

        $offer = Offer::active()->with('services')->find($request->offer_id);

        if (! $offer) {
            return redirect()->back()->with('cart_error', 'This offer is no longer available.');
        }

        if ($offer->services->isEmpty()) {
            return redirect()->back()->with('cart_error', 'This offer has no services attached.');
        }

        $hasCombo = $this->cartQuery()->whereNotNull('combo_offer_id')->exists();
        if ($hasCombo) {
            return redirect()->back()->with('cart_error', 'You can only add one combo offer to your cart. Please remove the existing offer first.');
        }

        foreach ($offer->services as $service) {
            $package = $service->pivot->package ?: 'basic';

            if ($service->has_packages) {
                $price = $package === 'premium'
                    ? ($service->premium_package_actual_price ?? $service->premium_package_price)
                    : ($service->basic_package_actual_price  ?? $service->basic_package_price);
            } else {
                $price = 0;
            }

            $lookup = auth()->check()
                ? ['user_id' => auth()->id(), 'service_id' => $service->id]
                : ['session_id' => session()->getId(), 'service_id' => $service->id];

            CartItem::updateOrCreate($lookup, [
                'package'        => $package,
                'price'          => $price,
                'original_price' => $price,
                'combo_offer_id' => $offer->id,
                'combo_group'    => 'offer_'. auth()->id() . '_'. $offer->id . '_' . time(),
                'service_name'   => $service->name,
                'image'          => $service->image,
            ]);
        }

        return redirect()->route('cart.index')
            ->with('cart_success', "Offer \"{$offer->title}\" has been applied to your cart.");
    }

    public function remove(CartItem $cartItem)
    {
        $owned = auth()->check()
            ? $cartItem->user_id === auth()->id()
            : $cartItem->session_id === session()->getId();

        abort_unless($owned, 403);

        if ($cartItem->combo_offer_id) {
            $this->cartQuery()
                ->where('combo_offer_id', $cartItem->combo_offer_id)
                ->delete();

            return redirect()->back()->with('cart_success', 'Offer and all its services have been removed from your cart.');
        }

        $cartItem->delete();

        return redirect()->back()->with('cart_success', 'Item removed from cart.');
    }

    public function clear()
    {
        $this->cartQuery()->delete();

        return redirect()->route('cart.index')->with('cart_success', 'Cart cleared.');
    }
}
