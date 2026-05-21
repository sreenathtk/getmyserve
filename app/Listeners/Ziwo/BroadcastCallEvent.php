<?php

namespace App\Listeners\Ziwo;

use App\Events\Ziwo\AgentStatusChanged;
use App\Events\Ziwo\CallAnswered;
use App\Events\Ziwo\CallEnded;
use App\Events\Ziwo\CallMissed;
use App\Events\Ziwo\CallStarted;
use App\Events\Ziwo\CallTransferred;
use App\Events\Ziwo\RecordingReady;
use App\Events\Ziwo\Broadcast\AdminCallEvent;

// NOT ShouldQueue — must broadcast synchronously for real-time effect
class BroadcastCallEvent
{
    public function handleCallStarted(CallStarted $event): void
    {
        broadcast(new AdminCallEvent('call.started', $event->payload, $event->payload['agentId'] ?? null));
    }

    public function handleCallAnswered(CallAnswered $event): void
    {
        broadcast(new AdminCallEvent('call.answered', $event->payload, $event->payload['agentId'] ?? null));
    }

    public function handleCallEnded(CallEnded $event): void
    {
        broadcast(new AdminCallEvent('call.ended', $event->payload, $event->payload['agentId'] ?? null));
    }

    public function handleCallMissed(CallMissed $event): void
    {
        broadcast(new AdminCallEvent('call.missed', $event->payload));
    }

    public function handleCallTransferred(CallTransferred $event): void
    {
        broadcast(new AdminCallEvent('call.transferred', $event->payload));
    }

    public function handleRecordingReady(RecordingReady $event): void
    {
        broadcast(new AdminCallEvent('recording.ready', $event->payload));
    }

    public function handleAgentStatusChanged(AgentStatusChanged $event): void
    {
        broadcast(new AdminCallEvent('agent.status_changed', $event->payload));
    }
}
