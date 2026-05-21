<?php

namespace App\Http\Controllers\Admin;

use App\Events\Customer\OrderCompleted;
use App\Events\Customer\OrderStatusUpdated;
use App\Events\Provider\ProviderOrderAssigned;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderFile;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Stripe\Refund;
use Stripe\Stripe;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'assignedStaff', 'assignedSp'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('refund_status')) {
            $query->where('refund_status', $request->refund_status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%")
                                                    ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        $orders    = $query->paginate(20)->withQueryString();
        $staff     = User::where('role', 'staff')->where('is_active', true)->orderBy('name')->get();
        $providers = User::where('role', 'service_provider')->where('is_active', true)->orderBy('name')->get();

        return view('admin.orders.index', compact('orders', 'staff', 'providers'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'assignedStaff', 'assignedSp', 'orderItems', 'files.uploader', 'payments']);
        $staff     = User::where('role', 'staff')->where('is_active', true)->orderBy('name')->get();
        $providers = User::where('role', 'service_provider')->where('is_active', true)->orderBy('name')->get();

        return view('admin.orders.show', compact('order', 'staff', 'providers'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate(['status' => 'required|in:pending,paid,processing,completed,cancelled,failed']);

        $oldStatus = $order->status;
        $order->update(['status' => $request->status]);

        if ($request->status !== $oldStatus) {
            event(new OrderStatusUpdated($order, $request->status));

            if ($request->status === 'completed') {
                event(new OrderCompleted($order));
            }
        }

        return redirect()->back()->with('success', 'Order status updated.');
    }

    public function assignStaff(Request $request, Order $order)
    {
        $request->validate(['staff_id' => 'nullable|exists:users,id']);

        $order->update(['assigned_staff_id' => $request->staff_id ?: null]);

        return redirect()->back()->with('success', 'Staff assignment updated.');
    }

    public function assignSp(Request $request, Order $order)
    {
        $request->validate(['sp_id' => 'nullable|exists:users,id']);

        $order->update(['assigned_sp_id' => $request->sp_id ?: null]);

        if ($request->filled('sp_id')) {
            event(new ProviderOrderAssigned($order));
        }

        return redirect()->back()->with('success', 'Service provider assignment updated.');
    }

    public function saveNotes(Request $request, Order $order)
    {
        $request->validate(['notes' => 'nullable|string|max:5000']);

        $order->update(['notes' => $request->notes]);

        return redirect()->back()->with('success', 'Notes saved.');
    }

    public function storeFiles(Request $request, Order $order)
    {
        $request->validate([
            'files'          => 'required|array|min:1',
            'files.*.name'   => 'required|string|max:255',
            'files.*.file'   => 'required|file|max:20480',
        ]);

        foreach ($request->file('files') as $index => $row) {
            $uploaded = $row['file'];
            $path     = $uploaded->store('order-files', 'public');

            OrderFile::create([
                'order_id'      => $order->id,
                'file_name'     => $request->input("files.{$index}.name"),
                'original_name' => $uploaded->getClientOriginalName(),
                'file_path'     => $path,
                'file_size'     => $uploaded->getSize(),
                'mime_type'     => $uploaded->getMimeType(),
                'uploaded_by'   => auth()->id(),
            ]);
        }

        return redirect()->back()->with('success', 'Files uploaded.');
    }

    public function destroyFile(Order $order, OrderFile $file)
    {
        abort_unless($file->order_id === $order->id, 403);

        Storage::disk('public')->delete($file->file_path);
        $file->delete();

        return redirect()->back()->with('success', 'File deleted.');
    }

    public function refund(Request $request, Order $order)
    {
        $request->validate([
            'refund_amount' => 'required|numeric|min:0.01|max:' . $order->getRefundableAmount(),
        ]);

        if (!$order->isRefundable()) {
            $message = $order->refund_status === 'full'
                ? 'This order has already been fully refunded.'
                : 'This order is not eligible for a refund (no Stripe payment found).';

            return redirect()->back()->with('error', $message);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $amountFils = (int) round($request->refund_amount * 100);

        $stripeRefund = Refund::create([
            'payment_intent' => $order->stripe_payment_intent_id,
            'amount'         => $amountFils,
        ]);

        $newRefunded   = (float) $order->refunded_amount + (float) $request->refund_amount;
        $refundStatus  = $newRefunded >= (float) $order->amount ? 'full' : 'partial';

        $order->update([
            'refunded_amount' => $newRefunded,
            'refund_status'   => $refundStatus,
            'status'          => $refundStatus === 'full' ? 'cancelled' : $order->status,
        ]);

        Payment::create([
            'order_id'         => $order->id,
            'stripe_refund_id' => $stripeRefund->id,
            'type'             => 'refund',
            'amount'           => $request->refund_amount,
            'currency'         => $order->currency,
            'status'           => 'succeeded',
            'metadata'         => ['reason' => $request->input('reason')],
        ]);

        return redirect()->back()->with('success', 'Refund of AED ' . number_format($request->refund_amount, 2) . ' processed successfully.');
    }
}
