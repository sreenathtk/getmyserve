<?php

namespace App\Http\Controllers;

use App\Events\Customer\OrderConfirmed;
use App\Models\CartItem;
use App\Models\Offer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Stripe\Coupon;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Webhook;

class CheckoutController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return ['auth'];
    }

    public function session()
    {
        $cartItems = CartItem::where('user_id', auth()->id())
            ->whereNotNull('price')
            ->where('price', '>', 0)
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('cart_error', 'No payable items in your cart. Items priced "on enquiry" cannot be checked out via payment.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        // Mirror the cart page discount logic for combo offers
        $comboOfferIds = $cartItems->pluck('combo_offer_id')->filter()->unique();
        $comboOffers   = $comboOfferIds->isNotEmpty()
            ? Offer::whereIn('id', $comboOfferIds)->get()->keyBy('id')
            : collect();

        $subtotal      = $cartItems->sum('price');
        $totalDiscount = 0;
        foreach ($comboOffers as $offerId => $comboOffer) {
            $groupSum = $cartItems->where('combo_offer_id', $offerId)->sum('price');
            $discount = $groupSum - $comboOffer->offer_price;
            if ($discount > 0) {
                $totalDiscount += $discount;
            }
        }
        $grandTotal = $subtotal - $totalDiscount;
        $vatRate    = (float) Setting::get('vat_rate', '0');
        $vatAmount  = $vatRate > 0 ? round($grandTotal * $vatRate / 100, 2) : 0;
        $finalTotal = $grandTotal + $vatAmount;

        $lineItems = $cartItems->map(function ($item) {
            $name = $item->service_name;
            if ($item->package !== 'standard') {
                $name .= ' (' . ucfirst($item->package) . ' Package)';
            }

            return [
                'price_data' => [
                    'currency'     => 'aed',
                    'unit_amount'  => (int) round($item->price * 100),
                    'product_data' => [
                        'name'   => $name,
                        'images' => $item->image ? [asset('storage/' . $item->image)] : [],
                    ],
                ],
                'quantity' => 1,
            ];
        })->values()->toArray();

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

        // Create a one-time coupon for the combo offer discount if applicable
        $discounts = [];
        if ($totalDiscount > 0) {
            $coupon    = Coupon::create([
                'amount_off' => (int) round($totalDiscount * 100),
                'currency'   => 'aed',
                'duration'   => 'once',
                'name'       => 'Combo Offer Discount',
            ]);
            $discounts = [['coupon' => $coupon->id]];
        }

        // Build offer_snapshot: one entry per distinct combo offer applied
        $offerSnapshot = [];
        foreach ($comboOffers as $offerId => $comboOffer) {
            $groupSum = $cartItems->where('combo_offer_id', $offerId)->sum('price');
            $discount = $groupSum - $comboOffer->offer_price;
            if ($discount > 0) {
                $offerSnapshot[] = [
                    'offer_id'       => $offerId,
                    'title'          => $comboOffer->title,
                    'original_price' => (float) $groupSum,
                    'offer_price'    => (float) $comboOffer->offer_price,
                    'discount'       => (float) $discount,
                ];
            }
        }

        $order = Order::create([
            'user_id'         => auth()->id(),
            'amount'          => $finalTotal,
            'discount_amount' => $totalDiscount,
            'vat_rate'        => $vatRate,
            'vat_amount'      => $vatAmount,
            'offer_snapshot'  => $offerSnapshot ?: null,
            'currency'        => 'AED',
            'status'          => 'pending',
            'items'           => $cartItems->map(fn($i) => [
                'service_name' => $i->service_name,
                'package'      => $i->package,
                'price'        => $i->price,
            ])->toArray(),
        ]);

        // Populate order_items with denormalised offer details for future display
        foreach ($cartItems as $cartItem) {
            $offerForItem = $cartItem->combo_offer_id
                ? $comboOffers->get($cartItem->combo_offer_id)
                : null;

            OrderItem::create([
                'order_id'          => $order->id,
                'service_id'        => $cartItem->service_id,
                'service_name'      => $cartItem->service_name,
                'package'           => $cartItem->package,
                'price'             => $cartItem->price,
                'original_price'    => $cartItem->original_price,
                'combo_offer_id'    => $cartItem->combo_offer_id,
                'combo_offer_title' => $offerForItem?->title,
                'combo_offer_price' => $offerForItem?->offer_price,
            ]);
        }

        $sessionParams = [
            'payment_method_types' => ['card'],
            'line_items'           => $lineItems,
            'mode'                 => 'payment',
            'client_reference_id'  => (string) $order->id,
            'customer_email'       => auth()->user()->email,
            'success_url'          => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'           => route('cart.index'),
        ];

        if ($discounts) {
            $sessionParams['discounts'] = $discounts;
        }

        $stripeSession = StripeSession::create($sessionParams);

        $order->update(['stripe_session_id' => $stripeSession->id]);

        return redirect($stripeSession->url);
    }

    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');

        if (! $sessionId) {
            return redirect()->route('cart.index');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $stripeSession = StripeSession::retrieve($sessionId);
        $order         = Order::where('stripe_session_id', $sessionId)
            ->where('user_id', auth()->id())
            ->first();

        if ($order && $stripeSession->payment_status === 'paid' && $order->status !== 'paid') {
            $order->update([
                'status'                   => 'paid',
                'stripe_payment_intent_id' => $stripeSession->payment_intent,
            ]);

            Payment::create([
                'order_id'                  => $order->id,
                'stripe_payment_intent_id'  => $stripeSession->payment_intent,
                'type'                      => 'charge',
                'amount'                    => $order->amount,
                'currency'                  => $order->currency,
                'status'                    => 'succeeded',
            ]);

            CartItem::where('user_id', auth()->id())
                ->whereNotNull('price')
                ->where('price', '>', 0)
                ->delete();

            event(new OrderConfirmed($order));
        }

        return view('checkout.success', compact('order'));
    }

    public function webhook(Request $request)
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret    = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $order   = Order::where('stripe_session_id', $session->id)->first();

            if ($order && $session->payment_status === 'paid') {
                $wasAlreadyPaid = $order->status === 'paid';

                $order->update([
                    'status'                   => 'paid',
                    'stripe_payment_intent_id' => $session->payment_intent,
                ]);

                // Only create payment record if not already created via success()
                if (! $wasAlreadyPaid && ! Payment::where('stripe_payment_intent_id', $session->payment_intent)->exists()) {
                    Payment::create([
                        'order_id'                  => $order->id,
                        'stripe_payment_intent_id'  => $session->payment_intent,
                        'type'                      => 'charge',
                        'amount'                    => $order->amount,
                        'currency'                  => $order->currency,
                        'status'                    => 'succeeded',
                    ]);

                    event(new OrderConfirmed($order));
                }
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
