<?php

namespace App\Events\Provider;

use App\Models\Enquiry;

class ProviderEnquiryAssigned
{
    public function __construct(
        public readonly Enquiry $enquiry,
    ) {}
}
