<?php

namespace App\Services\Ziwo;

use App\Models\CallAgent;

class ZiwoAgentService
{
    public function __construct(private ZiwoApiClient $client) {}

    public function getAgentToken(string $ziwoAgentId): string
    {
        $result = $this->client->post('/agents/token', ['agentId' => $ziwoAgentId]);

        return $result['token'] ?? throw new \RuntimeException('ZIWO agent token not returned.');
    }

    public function getAllAgents(): array
    {
        return $this->client->get('/agents');
    }

    public function syncAgents(): int
    {
        $ziwoAgents = $this->getAllAgents();
        $synced     = 0;

        foreach ($ziwoAgents as $ziwoAgent) {
            CallAgent::updateOrCreate(
                ['ziwo_agent_id' => $ziwoAgent['id']],
                [
                    'ziwo_extension' => $ziwoAgent['extension'] ?? null,
                    'ziwo_username'  => $ziwoAgent['username'],
                    'display_name'   => $ziwoAgent['name'] ?? $ziwoAgent['username'],
                    'is_active'      => $ziwoAgent['active'] ?? true,
                ]
            );
            $synced++;
        }

        return $synced;
    }

    public function updateStatus(string $ziwoAgentId, string $status): void
    {
        CallAgent::where('ziwo_agent_id', $ziwoAgentId)->update([
            'status'                 => $status,
            'last_status_changed_at' => now(),
        ]);
    }
}
