<?php

namespace App\Listeners\WhatsApp;

use App\Events\Customer\OrderCompleted;
use App\Jobs\SendWatiMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyCustomerOrderCompleted implements ShouldQueue
{
    public function handle(OrderCompleted $event): void
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

        SendWatiMessage::dispatch(
            $phone,
            config('wati.templates.customer_order_completed'),
            [$user->name, (string) $order->id],
        );
    }
}
