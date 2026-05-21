<?php

namespace App\Events\Customer;

use App\Models\Enquiry;

class EnquirySubmitted
{
    public function __construct(
        public readonly Enquiry $enquiry,
    ) {}
}
