<?php

namespace App\Services\Ziwo;

use App\Events\Ziwo\AgentStatusChanged;
use App\Events\Ziwo\CallAnswered;
use App\Events\Ziwo\CallEnded;
use App\Events\Ziwo\CallMissed;
use App\Events\Ziwo\CallStarted;
use App\Events\Ziwo\CallTransferred;
use App\Events\Ziwo\RecordingReady;
use App\Models\WebhookLog;
use Illuminate\Support\Facades\Log;

class ZiwoWebhookProcessor
{
    public function process(WebhookLog $webhookLog): void
    {
        $payload   = $webhookLog->payload;
        $eventType = $webhookLog->event_type;

        try {
            match ($eventType) {
                'call.created'         => event(new CallStarted($payload)),
                'call.answered'        => event(new CallAnswered($payload)),
                'call.ended'           => event(new CallEnded($payload)),
                'call.missed'          => event(new CallMissed($payload)),
                'call.transferred'     => event(new CallTransferred($payload)),
                'recording.completed'  => event(new RecordingReady($payload)),
                'agent.status_changed' => event(new AgentStatusChanged($payload)),
                default => Log::channel('ziwo')->warning('Unhandled webhook event.', [
                    'event' => $eventType,
                ]),
            };

            $webhookLog->update([
                'is_processed' => true,
                'processed_at' => now(),
            ]);
        } catch (\Throwable $e) {
            $webhookLog->increment('attempts');
            $webhookLog->update(['last_error' => $e->getMessage()]);
            throw $e;
        }
    }
}
