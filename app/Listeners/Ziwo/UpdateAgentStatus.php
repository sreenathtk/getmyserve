<?php

namespace App\Listeners\Ziwo;

use App\Events\Ziwo\AgentStatusChanged;
use App\Services\Ziwo\ZiwoAgentService;

class UpdateAgentStatus
{
    public function __construct(private ZiwoAgentService $agentService) {}

    public function handle(AgentStatusChanged $event): void
    {
        $this->agentService->updateStatus(
            $event->payload['agentId'],
            $event->payload['status']
        );
    }
}
