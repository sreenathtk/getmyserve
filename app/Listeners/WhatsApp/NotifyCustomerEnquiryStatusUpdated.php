<?php

namespace App\Listeners\WhatsApp;

use App\Events\Customer\EnquiryStatusUpdated;
use App\Jobs\SendWatiMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyCustomerEnquiryStatusUpdated implements ShouldQueue
{
    private const STATUS_LABELS = [
        'pending'          => 'Pending',
        'in_progress'      => 'In Progress',
        'under_processing' => 'Under Processing',
        'completed'        => 'Completed',
        'resolved'         => 'Resolved',
    ];

    public function handle(EnquiryStatusUpdated $event): void
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

        $label = self::STATUS_LABELS[$event->newStatus] ?? ucfirst($event->newStatus);

        SendWatiMessage::dispatch(
            $phone,
            config('wati.templates.customer_enquiry_status'),
            [$enquiry->full_name, (string) $enquiry->id, $label],
        );
    }
}
