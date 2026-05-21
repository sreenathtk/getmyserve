<?php

namespace App\Events\Customer;

use App\Models\Enquiry;

class EnquiryCompleted
{
    public function __construct(
        public readonly Enquiry $enquiry,
    ) {}
}
