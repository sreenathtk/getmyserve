<?php

namespace App\Listeners\WhatsApp;

use App\Events\Provider\ProviderOrderAssigned;
use App\Jobs\SendWatiMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyProviderOrderAssigned implements ShouldQueue
{
    public function handle(ProviderOrderAssigned $event): void
    {
        $order = $event->order;
        $order->loadMissing(['assignedSp.serviceProvider', 'user']);

        $spUser = $order->assignedSp;
        $sp     = $spUser?->serviceProvider;
        $phone  = $sp?->whatsapp_number ?? $sp?->comm_mobile;

        if (empty($phone)) {
            return;
        }

        SendWatiMessage::dispatch(
            $phone,
            config('wati.templates.provider_order_assigned'),
            [
                $sp->company_name,
                (string) $order->id,
                $order->user?->name ?? 'Customer',
            ],
        );
    }
}
