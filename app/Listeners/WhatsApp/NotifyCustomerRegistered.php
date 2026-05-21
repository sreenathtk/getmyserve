<?php

namespace App\Listeners\WhatsApp;

use App\Events\Customer\CustomerRegistered;
use App\Jobs\SendWatiMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyCustomerRegistered implements ShouldQueue
{
    public function handle(CustomerRegistered $event): void
    {
        $user  = $event->user;

        if (! ($user->whatsapp_notifications ?? true)) {
            return;
        }

        $phone = $user->whatsapp_number ?? $user->phone;

        if (empty($phone)) {
            return;
        }

        SendWatiMessage::dispatch(
            $phone,
            config('wati.templates.customer_welcome'),
            [$user->name],
        );
    }
}
