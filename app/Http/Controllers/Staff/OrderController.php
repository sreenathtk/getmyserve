<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user'])
            ->where('assigned_staff_id', auth()->id())
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(20)->withQueryString();

        return view('staff.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        abort_unless($order->assigned_staff_id === auth()->id(), 403);

        $order->load(['user', 'assignedSp.serviceProvider', 'orderItems', 'files.uploader', 'payments']);
        $providers = User::where('role', 'service_provider')->where('is_active', true)->orderBy('name')->get();

        return view('staff.orders.show', compact('order', 'providers'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        abort_unless($order->assigned_staff_id === auth()->id(), 403);

        $request->validate(['status' => 'required|in:processing,completed']);

        $order->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Order status updated.');
    }

    public function assignSp(Request $request, Order $order)
    {
        abort_unless($order->assigned_staff_id === auth()->id(), 403);

        $request->validate(['sp_id' => 'nullable|exists:users,id']);

        $order->update(['assigned_sp_id' => $request->sp_id ?: null]);

        return redirect()->back()->with('success', 'Service provider assignment updated.');
    }
}
