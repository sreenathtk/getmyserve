<?php

namespace App\Events\Customer;

use App\Models\Order;

class OrderConfirmed
{
    public function __construct(
        public readonly Order $order,
    ) {}
}
