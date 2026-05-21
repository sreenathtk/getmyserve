<?php

namespace App\Services\Ziwo;

use Illuminate\Support\Facades\Log;

class ZiwoCallService
{
    public function __construct(private ZiwoApiClient $client) {}

    public function dial(string $toNumber, string $agentId, ?string $callerId = null): array
    {
        $payload = [
            'to'    => $this->normalisePhone($toNumber),
            'agent' => $agentId,
        ];

        if ($callerId) {
            $payload['callerId'] = $callerId;
        }

        $result = $this->client->post('/calls/dial', $payload);

        Log::channel('ziwo')->info('Outbound call initiated.', [
            'to'     => $toNumber,
            'agent'  => $agentId,
            'result' => $result,
        ]);

        return $result;
    }

    public function getCall(string $ziwoCallId): array
    {
        return $this->client->get("/calls/{$ziwoCallId}");
    }

    public function getCallHistory(array $filters = []): array
    {
        return $this->client->get('/calls', $filters);
    }

    public function getLiveCalls(): array
    {
        return $this->client->get('/calls/live');
    }

    private function normalisePhone(string $phone): string
    {
        return preg_replace('/[^0-9+]/', '', $phone);
    }
}
