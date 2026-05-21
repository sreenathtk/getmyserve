<?php

namespace App\Listeners\WhatsApp;

use App\Events\Customer\OrderStatusUpdated;
use App\Jobs\SendWatiMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyCustomerOrderStatusUpdated implements ShouldQueue
{
    private const STATUS_LABELS = [
        'pending'    => 'Pending',
        'paid'       => 'Payment Confirmed',
        'processing' => 'Processing',
        'completed'  => 'Completed',
        'cancelled'  => 'Cancelled',
        'failed'     => 'Failed',
    ];

    public function handle(OrderStatusUpdated $event): void
    {
        $order = $event->order;
        $order->loadMissing('user');

        $user = $order->user;

        if (! ($user?->whatsapp_notifications ?? true)) {
            return;
        }

        $phone = $user?->whatsapp_number ?? $user?->phone;

        if (empty($phone)) {
            return;
        }

        $label = self::STATUS_LABELS[$event->newStatus] ?? ucfirst($event->newStatus);

        SendWatiMessage::dispatch(
            $phone,
            config('wati.templates.customer_order_status'),
            [$user->name, (string) $order->id, $label],
        );
    }
}
