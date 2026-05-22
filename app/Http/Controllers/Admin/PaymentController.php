<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['order.user', 'enquiry'])->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('stripe_payment_intent_id', 'like', "%{$search}%")
                  ->orWhere('stripe_refund_id', 'like', "%{$search}%")
                  ->orWhereHas('order', fn($o) => $o->where('id', 'like', "%{$search}%"))
                  ->orWhereHas('order.user', fn($u) => $u->where('name', 'like', "%{$search}%")
                                                          ->orWhere('email', 'like', "%{$search}%"))
                  ->orWhereHas('enquiry', fn($eq) => $eq->where('id', 'like', "%{$search}%")
                                                         ->orWhere('full_name', 'like', "%{$search}%")
                                                         ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        $payments = $query->paginate(25)->withQueryString();

        $totalCharged  = Payment::where('type', 'charge')->where('status', 'succeeded')->sum('amount');
        $totalRefunded = Payment::where('type', 'refund')->where('status', 'succeeded')->sum('amount');

        return view('admin.payments.index', compact('payments', 'totalCharged', 'totalRefunded'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['order.user', 'order.orderItems', 'enquiry.service']);

        return view('admin.payments.show', compact('payment'));
    }
}
