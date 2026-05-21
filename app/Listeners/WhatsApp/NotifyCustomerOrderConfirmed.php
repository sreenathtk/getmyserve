<?php

namespace App\Listeners\WhatsApp;

use App\Events\Customer\OrderConfirmed;
use App\Jobs\SendWatiMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyCustomerOrderConfirmed implements ShouldQueue
{
    public function handle(OrderConfirmed $event): void
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
            config('wati.templates.customer_order_confirmed'),
            [$user->name, (string) $order->id, number_format((float) $order->amount, 2)],
        );
    }
}
