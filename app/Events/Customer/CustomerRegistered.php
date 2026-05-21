<?php

namespace App\Events\Customer;

use App\Models\User;

class CustomerRegistered
{
    public function __construct(
        public readonly User $user,
    ) {}
}
