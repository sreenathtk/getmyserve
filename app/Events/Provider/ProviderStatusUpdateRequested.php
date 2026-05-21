<?php

namespace App\Events\Provider;

use App\Models\Enquiry;

class ProviderStatusUpdateRequested
{
    public function __construct(
        public readonly Enquiry $enquiry,
        public readonly string  $note,
    ) {}
}
