<?php

namespace App\Events\Customer;

use App\Models\Enquiry;

class EnquiryStatusUpdated
{
    public function __construct(
        public readonly Enquiry $enquiry,
        public readonly string  $newStatus,
    ) {}
}
