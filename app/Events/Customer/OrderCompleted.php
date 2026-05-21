<?php

namespace App\Events\Customer;

use App\Models\Order;

class OrderCompleted
{
    public function __construct(
        public readonly Order $order,
    ) {}
}
