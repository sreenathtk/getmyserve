<?php

namespace App\Listeners\WhatsApp;

use App\Events\Customer\EnquirySubmitted;
use App\Jobs\SendWatiMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyCustomerEnquirySubmitted implements ShouldQueue
{
    public function handle(EnquirySubmitted $event): void
    {
        $enquiry = $event->enquiry;

        if ($enquiry->user_id) {
            $enquiry->loadMissing('customer');
            if (! ($enquiry->customer?->whatsapp_notifications ?? true)) {
                return;
            }
        }

        $phone = $enquiry->whatsapp ?? $enquiry->phone;

        if (empty($phone)) {
            return;
        }

        $enquiry->loadMissing('service');

        SendWatiMessage::dispatch(
            $phone,
            config('wati.templates.customer_enquiry_received'),
            [$enquiry->full_name, $enquiry->service?->name ?? 'your service', (string) $enquiry->id],
        );
    }
}
