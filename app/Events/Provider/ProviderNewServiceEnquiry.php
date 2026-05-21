<?php

namespace App\Events\Provider;

use App\Models\Enquiry;

class ProviderNewServiceEnquiry
{
    public function __construct(
        public readonly Enquiry $enquiry,
    ) {}
}
