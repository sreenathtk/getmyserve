<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class PackageController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth', only: ['checkout']),
        ];
    }

    public function show(Offer $offer)
    {
        if (! $offer->is_currently_active) {
            return redirect()->route('home')->with('error', 'This offer is no longer available.');
        }

        $offer->loadMissing('services');

        $offerPrice   = (float) $offer->offer_price;
        $vatRate      = (float) Setting::get('vat_rate', '0');
        $vatAmount    = $vatRate > 0 ? round($offerPrice * $vatRate / 100, 2) : 0;
        $totalWithVat = $offerPrice + $vatAmount;

        return view('packages.show', compact('offer', 'offerPrice', 'vatRate', 'vatAmount', 'totalWithVat'));
    }

    public function checkout(Offer $offer)
    {
        if (! $offer->is_currently_active) {
            return redirect()->route('packages.show', $offer)->with('error', 'This offer is no longer available.');
        }

        $offer->loadMissing('services');

        // Build service list — all services included; price = 0 for those without pricing
        $serviceItems = $offer->services->map(function ($service) {
            $package = $service->pivot->package ?: 'basic';
            if ($service->has_packages) {
                $price = $package === 'premium'
                    ? ($service->premium_package_actual_price ?? $service->premium_package_price ?? 0)
                    : ($service->basic_package_actual_price   ?? $service->basic_package_price   ?? 0);
            } else {
                $price = 0;
            }
            return [
                'service' => $service,
                'package' => $package,
                'price'   => (float) $price,
            ];
        })->values()->all();

        // The charge is always the offer price — individual service prices are for record-keeping only
        $offerPrice = (float) $offer->offer_price;
        $vatRate    = (float) Setting::get('vat_rate', '0');
        $vatAmount  = $vatRate > 0 ? round($offerPrice * $vatRate / 100, 2) : 0;
        $finalTotal = $offerPrice + $vatAmount;

        Stripe::setApiKey(config('services.stripe.secret'));

        // Single line item for the package at offer_price
        $lineItems = [[
            'price_data' => [
                'currency'     => 'aed',
                'unit_amount'  => (int) round($offerPrice * 100),
                'product_data' => ['name' => $offer->title . ' Package'],
            ],
            'quantity' => 1,
        ]];

        if ($vatAmount > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency'     => 'aed',
                    'unit_amount'  => (int) round($vatAmount * 100),
                    'product_data' => ['name' => 'VAT (' . $vatRate . '%)'],
                ],
                'quantity' => 1,
            ];
        }

        $order = Order::create([
            'user_id'         => auth()->id(),
            'amount'          => $finalTotal,
            'discount_amount' => 0,
            'vat_rate'        => $vatRate,
            'vat_amount'      => $vatAmount,
            'offer_snapshot'  => [[
                'offer_id'    => $offer->id,
                'title'       => $offer->title,
                'offer_price' => $offerPrice,
            ]],
            'currency'        => 'AED',
            'status'          => 'pending',
            'items'           => array_map(fn($i) => [
                'service_name' => $i['service']->name,
                'package'      => $i['package'],
                'price'        => $i['price'],
            ], $serviceItems),
        ]);

        foreach ($serviceItems as $item) {
            OrderItem::create([
                'order_id'          => $order->id,
                'service_id'        => $item['service']->id,
                'service_name'      => $item['service']->name,
                'package'           => $item['package'],
                'price'             => $item['price'],
                'original_price'    => $item['price'],
                'combo_offer_id'    => $offer->id,
                'combo_offer_title' => $offer->title,
                'combo_offer_price' => $offer->offer_price,
            ]);
        }

        $stripeSession = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items'           => $lineItems,
            'mode'                 => 'payment',
            'client_reference_id'  => (string) $order->id,
            'customer_email'       => auth()->user()->email,
            'success_url'          => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'           => route('packages.show', $offer),
            'metadata'             => ['source' => 'package'],
        ]);

        $order->update(['stripe_session_id' => $stripeSession->id]);

        return redirect($stripeSession->url);
    }
}
