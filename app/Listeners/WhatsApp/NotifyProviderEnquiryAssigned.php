<?php

namespace App\Listeners\WhatsApp;

use App\Events\Provider\ProviderEnquiryAssigned;
use App\Jobs\SendWatiMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyProviderEnquiryAssigned implements ShouldQueue
{
    public function handle(ProviderEnquiryAssigned $event): void
    {
        $enquiry = $event->enquiry;
        $enquiry->loadMissing(['assignedSp.serviceProvider', 'service']);

        $spUser = $enquiry->assignedSp;
        $sp     = $spUser?->serviceProvider;
        $phone  = $sp?->whatsapp_number ?? $sp?->comm_mobile;

        if (empty($phone)) {
            return;
        }

        SendWatiMessage::dispatch(
            $phone,
            config('wati.templates.provider_enquiry_assigned'),
            [
                $sp->company_name,
                $enquiry->service?->name ?? 'N/A',
                (string) $enquiry->id,
                $enquiry->full_name,
            ],
        );
    }
}
