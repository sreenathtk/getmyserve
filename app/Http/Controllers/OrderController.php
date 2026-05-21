<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;

class OrderController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return ['auth'];
    }

    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->latest()
            ->paginate(15);

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        $order->load(['orderItems', 'payments']);

        return view('orders.show', compact('order'));
    }

    public function requestRefund(Request $request, Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);
        abort_unless($order->status === 'paid' && $order->refund_status === 'none', 422);

        $request->validate(['reason' => 'nullable|string|max:1000']);

        $order->update([
            'refund_status' => 'requested',
            'notes'         => $request->reason
                ? 'Refund requested by customer: ' . $request->reason
                : 'Refund requested by customer.',
        ]);

        return redirect()->back()->with('success', 'Your refund request has been submitted. Our team will review it shortly.');
    }

    /**
     * Cancel a single order item (or its entire combo group).
     *
     * If the item belongs to a combo offer, ALL items in that combo on this order
     * are cancelled together — the customer is warned in the UI before confirming.
     * The refund amount is set to the combo_offer_price (for combos) or the item
     * price (for standalone items) and stored as a refund request for admin to process.
     */
    public function cancelItem(Request $request, Order $order, OrderItem $item)
    {
        abort_unless($order->user_id === auth()->id(), 403);
        abort_unless($item->order_id === $order->id, 403);
        abort_unless($order->status === 'paid', 422);
        abort_unless($item->status === OrderItem::STATUS_ACTIVE, 422);

        if ($item->isCombo()) {
            // Cancel every active item in this combo group
            $siblings = $order->orderItems
                ->where('combo_offer_id', $item->combo_offer_id)
                ->where('status', OrderItem::STATUS_ACTIVE);

            foreach ($siblings as $sibling) {
                $sibling->update(['status' => OrderItem::STATUS_CANCELLED]);
            }

            // Refund amount = the combo's offer_price (what was actually charged for this bundle)
            $refundAmount = (float) $item->combo_offer_price;
            $note = 'Item cancellation requested by customer: combo offer "' . $item->combo_offer_title . '" cancelled.';
        } else {
            $item->update(['status' => OrderItem::STATUS_CANCELLED]);
            $refundAmount = $item->individualRefundAmount();
            $note = 'Item cancellation requested by customer: "' . $item->service_name . '" cancelled.';
        }

        // If every item is now cancelled, mark the order as cancelled
        $order->load('orderItems');
        $allCancelled = $order->orderItems->every(fn($i) => $i->status === OrderItem::STATUS_CANCELLED);

        $order->update([
            'refund_status' => 'requested',
            'status'        => $allCancelled ? 'cancelled' : $order->status,
            'notes'         => ($order->notes ? $order->notes . "\n" : '') . $note,
        ]);

        return response()->json([
            'ok'            => true,
            'refund_amount' => $refundAmount,
            'message'       => 'Cancellation submitted. Our team will process your refund of AED ' . number_format($refundAmount, 2) . '.',
        ]);
    }
}
