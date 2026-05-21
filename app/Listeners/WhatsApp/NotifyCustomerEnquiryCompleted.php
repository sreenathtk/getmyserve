<?php

namespace App\Listeners\WhatsApp;

use App\Events\Customer\EnquiryCompleted;
use App\Jobs\SendWatiMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyCustomerEnquiryCompleted implements ShouldQueue
{
    public function handle(EnquiryCompleted $event): void
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
            config('wati.templates.customer_enquiry_completed'),
            [$enquiry->full_name, $enquiry->service?->name ?? 'your service', (string) $enquiry->id],
        );
    }
}
