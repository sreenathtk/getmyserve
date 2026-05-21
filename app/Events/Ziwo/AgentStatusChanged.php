<?php

namespace App\Events\Ziwo;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AgentStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly array $payload) {}
}
