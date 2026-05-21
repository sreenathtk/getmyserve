<?php

namespace App\Events\Ziwo\Broadcast;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class AdminCallEvent implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly string  $eventName,
        public readonly array   $data,
        public readonly ?string $agentId = null,
    ) {}

    public function broadcastOn(): array
    {
        $channels = [new PrivateChannel('admin-calls')];

        if ($this->agentId) {
            $channels[] = new PrivateChannel("agent.{$this->agentId}");
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return $this->eventName;
    }

    public function broadcastWith(): array
    {
        return $this->data;
    }
}
