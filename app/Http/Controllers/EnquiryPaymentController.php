<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\Payment;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class EnquiryPaymentController extends Controller
{
    public function checkout(Enquiry $enquiry)
    {
        abort_unless($enquiry->user_id === auth()->id(), 403);

        if (!$enquiry->isQuoted()) {
            return redirect()->route('profile.enquiry', $enquiry)
                ->with('error', 'No price has been set for this request yet.');
        }

        if ($enquiry->isPaid()) {
            return redirect()->route('profile.enquiry', $enquiry)
                ->with('info', 'This request has already been paid.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $stripeSession = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items'           => [[
                'price_data' => [
                    'currency'     => 'aed',
                    'unit_amount'  => (int) round((float) $enquiry->quoted_price * 100),
                    'product_data' => [
                        'name' => 'Service Request #' . str_pad($enquiry->id, 5, '0', STR_PAD_LEFT)
                            . ' — ' . ($enquiry->service?->name ?? 'Service'),
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode'                => 'payment',
            'customer_email'      => auth()->user()->email,
            'metadata'            => [
                'type'       => 'enquiry',
                'enquiry_id' => $enquiry->id,
            ],
            'success_url' => route('enquiries.payment.success', $enquiry)
                . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('profile.enquiry', $enquiry),
        ]);

        $enquiry->update(['stripe_session_id' => $stripeSession->id]);

        return redirect($stripeSession->url);
    }

    public function success(Request $request, Enquiry $enquiry)
    {
        abort_unless($enquiry->user_id === auth()->id(), 403);

        $sessionId = $request->query('session_id');

        if ($sessionId && !$enquiry->isPaid()) {
            Stripe::setApiKey(config('services.stripe.secret'));

            $stripeSession = StripeSession::retrieve($sessionId);

            if ($stripeSession->payment_status === 'paid') {
                $enquiry->update([
                    'payment_status'           => 'paid',
                    'paid_amount'              => $enquiry->quoted_price,
                    'paid_at'                  => now(),
                    'stripe_payment_intent_id' => $stripeSession->payment_intent,
                ]);

                if (!Payment::where('stripe_payment_intent_id', $stripeSession->payment_intent)
                             ->where('enquiry_id', $enquiry->id)->exists()) {
                    Payment::create([
                        'enquiry_id'               => $enquiry->id,
                        'stripe_payment_intent_id' => $stripeSession->payment_intent,
                        'type'                     => 'charge',
                        'amount'                   => $enquiry->quoted_price,
                        'currency'                 => 'AED',
                        'status'                   => 'succeeded',
                    ]);
                }
            }
        }

        return redirect()->route('profile.enquiry', $enquiry)
            ->with('payment_success', 'Payment successful! Your service request is now confirmed.');
    }
}
