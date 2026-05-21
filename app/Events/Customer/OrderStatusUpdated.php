<?php

namespace App\Events\Customer;

use App\Models\Order;

class OrderStatusUpdated
{
    public function __construct(
        public readonly Order  $order,
        public readonly string $newStatus,
    ) {}
}
