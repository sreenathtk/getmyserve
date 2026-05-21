<?php

namespace App\Listeners\WhatsApp;

use App\Events\Provider\ProviderStatusUpdateRequested;
use App\Jobs\SendWatiMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyProviderStatusUpdateRequested implements ShouldQueue
{
    public function handle(ProviderStatusUpdateRequested $event): void
    {
        $enquiry = $event->enquiry;
        $enquiry->loadMissing('assignedSp.serviceProvider');

        $spUser = $enquiry->assignedSp;
        $sp     = $spUser?->serviceProvider;
        $phone  = $sp?->whatsapp_number ?? $sp?->comm_mobile;

        if (empty($phone)) {
            return;
        }

        SendWatiMessage::dispatch(
            $phone,
            config('wati.templates.provider_status_request'),
            [
                $sp->company_name,
                (string) $enquiry->id,
                $event->note,
            ],
        );
    }
}
