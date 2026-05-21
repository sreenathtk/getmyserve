<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'orderItems'])
            ->where('assigned_sp_id', auth()->id())
            ->latest()
            ->get();

        return view('provider.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        abort_unless($order->assigned_sp_id === auth()->id(), 403);

        $order->load(['user', 'orderItems', 'files', 'payments']);

        return view('provider.orders.show', compact('order'));
    }
}
