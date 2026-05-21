<?php

namespace App\Events\Provider;

use App\Models\Order;

class ProviderOrderAssigned
{
    public function __construct(
        public readonly Order $order,
    ) {}
}
