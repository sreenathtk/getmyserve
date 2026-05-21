<?php

namespace App\Providers;

use App\Events\Customer\CustomerRegistered;
use App\Events\Customer\EnquiryCompleted;
use App\Events\Customer\EnquiryStatusUpdated;
use App\Events\Customer\EnquirySubmitted;
use App\Events\Customer\OrderCompleted;
use App\Events\Customer\OrderConfirmed;
use App\Events\Customer\OrderStatusUpdated;
use App\Events\Provider\ProviderEnquiryAssigned;
use App\Events\Provider\ProviderNewServiceEnquiry;
use App\Events\Provider\ProviderOrderAssigned;
use App\Events\Provider\ProviderStatusUpdateRequested;
use App\Events\Ziwo\AgentStatusChanged;
use App\Events\Ziwo\CallAnswered;
use App\Events\Ziwo\CallEnded;
use App\Events\Ziwo\CallMissed;
use App\Events\Ziwo\CallStarted;
use App\Events\Ziwo\CallTransferred;
use App\Events\Ziwo\RecordingReady;
use App\Listeners\WhatsApp\NotifyCustomerEnquiryCompleted;
use App\Listeners\WhatsApp\NotifyCustomerEnquiryStatusUpdated;
use App\Listeners\WhatsApp\NotifyCustomerEnquirySubmitted;
use App\Listeners\WhatsApp\NotifyCustomerOrderCompleted;
use App\Listeners\WhatsApp\NotifyCustomerOrderConfirmed;
use App\Listeners\WhatsApp\NotifyCustomerOrderStatusUpdated;
use App\Listeners\WhatsApp\NotifyCustomerRegistered;
use App\Listeners\WhatsApp\NotifyProviderEnquiryAssigned;
use App\Listeners\WhatsApp\NotifyProviderNewServiceEnquiry;
use App\Listeners\WhatsApp\NotifyProviderOrderAssigned;
use App\Listeners\WhatsApp\NotifyProviderStatusUpdateRequested;
use App\Listeners\Ziwo\BroadcastCallEvent;
use App\Listeners\Ziwo\CreateCallLog;
use App\Listeners\Ziwo\CreateCommunicationHistory;
use App\Listeners\Ziwo\DispatchRecordingSync;
use App\Listeners\Ziwo\UpdateAgentStatus;
use App\Listeners\Ziwo\UpdateCallLog;
use App\View\Composers\SettingComposer;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer(['layouts.footer', 'contact'], SettingComposer::class);

        // WhatsApp event → listener bindings
        Event::listen(CustomerRegistered::class,          NotifyCustomerRegistered::class);
        Event::listen(EnquirySubmitted::class,            NotifyCustomerEnquirySubmitted::class);
        Event::listen(EnquiryStatusUpdated::class,        NotifyCustomerEnquiryStatusUpdated::class);
        Event::listen(EnquiryCompleted::class,            NotifyCustomerEnquiryCompleted::class);
        Event::listen(OrderConfirmed::class,              NotifyCustomerOrderConfirmed::class);
        Event::listen(OrderStatusUpdated::class,          NotifyCustomerOrderStatusUpdated::class);
        Event::listen(OrderCompleted::class,              NotifyCustomerOrderCompleted::class);
        Event::listen(ProviderEnquiryAssigned::class,     NotifyProviderEnquiryAssigned::class);
        Event::listen(ProviderOrderAssigned::class,       NotifyProviderOrderAssigned::class);
        Event::listen(ProviderStatusUpdateRequested::class, NotifyProviderStatusUpdateRequested::class);
        Event::listen(ProviderNewServiceEnquiry::class,   NotifyProviderNewServiceEnquiry::class);

        // ── ZIWO Call Center event → listener bindings ─────────────────────────
        Event::listen(CallStarted::class,     [CreateCallLog::class, 'handle']);
        Event::listen(CallStarted::class,     [BroadcastCallEvent::class, 'handleCallStarted']);

        Event::listen(CallAnswered::class,    [UpdateCallLog::class, 'handleAnswered']);
        Event::listen(CallAnswered::class,    [BroadcastCallEvent::class, 'handleCallAnswered']);

        Event::listen(CallEnded::class,       [UpdateCallLog::class, 'handleEnded']);
        Event::listen(CallEnded::class,       [CreateCommunicationHistory::class, 'handle']);
        Event::listen(CallEnded::class,       [BroadcastCallEvent::class, 'handleCallEnded']);

        Event::listen(CallMissed::class,      [UpdateCallLog::class, 'handleMissed']);
        Event::listen(CallMissed::class,      [BroadcastCallEvent::class, 'handleCallMissed']);

        Event::listen(CallTransferred::class, [UpdateCallLog::class, 'handleTransferred']);
        Event::listen(CallTransferred::class, [BroadcastCallEvent::class, 'handleCallTransferred']);

        Event::listen(RecordingReady::class,  [DispatchRecordingSync::class, 'handle']);
        Event::listen(RecordingReady::class,  [BroadcastCallEvent::class, 'handleRecordingReady']);

        Event::listen(AgentStatusChanged::class, [UpdateAgentStatus::class, 'handle']);
        Event::listen(AgentStatusChanged::class, [BroadcastCallEvent::class, 'handleAgentStatusChanged']);
    }
}
