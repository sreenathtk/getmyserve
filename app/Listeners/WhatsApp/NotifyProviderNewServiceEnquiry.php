<?php

namespace App\Listeners\WhatsApp;

use App\Events\Provider\ProviderNewServiceEnquiry;
use App\Jobs\SendWatiMessage;
use App\Models\ServiceProvider;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyProviderNewServiceEnquiry implements ShouldQueue
{
    public function handle(ProviderNewServiceEnquiry $event): void
    {
        $enquiry = $event->enquiry;
        $enquiry->loadMissing('service');

        $serviceName = $enquiry->service?->name ?? 'N/A';

        $providers = ServiceProvider::whereHas(
            'services',
            fn ($q) => $q->where('services.id', $enquiry->service_id)->where('service_provider_services.is_active', true)
        )->where('is_active', true)->get();

        foreach ($providers as $sp) {
            $phone = $sp->whatsapp_number ?? $sp->comm_mobile;

            if (empty($phone)) {
                continue;
            }

            SendWatiMessage::dispatch(
                $phone,
                config('wati.templates.provider_new_service_enquiry'),
                [$sp->company_name, $serviceName, $enquiry->full_name],
            );
        }
    }
}
