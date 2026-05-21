<?php

namespace App\Listeners\Ziwo;

use App\Events\Ziwo\CallStarted;
use App\Models\ActiveCall;
use App\Models\CallAgent;
use App\Models\CallLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;

class CreateCallLog implements ShouldQueue
{
    public string $queue = 'default';

    public function handle(CallStarted $event): void
    {
        $p = $event->payload;

        $agent = CallAgent::where('ziwo_agent_id', $p['agentId'] ?? null)->first();

        $log = CallLog::create([
            'ziwo_call_id'  => $p['callId'],
            'direction'     => $p['direction'] ?? 'inbound',
            'status'        => 'initiated',
            'caller_number' => $p['from'] ?? '',
            'callee_number' => $p['to'] ?? '',
            'caller_name'   => $p['fromName'] ?? null,
            'callee_name'   => $p['toName'] ?? null,
            'agent_id'      => $agent?->id,
            'queue_name'    => $p['queue'] ?? null,
            'started_at'    => now(),
            'ziwo_metadata' => $p,
        ]);

        ActiveCall::create([
            'ziwo_call_id'   => $p['callId'],
            'call_log_id'    => $log->id,
            'agent_id'       => $agent?->id,
            'caller_number'  => $log->caller_number,
            'callee_number'  => $log->callee_number,
            'status'         => 'ringing',
            'started_at'     => now(),
            'last_updated_at'=> now(),
        ]);

        // Attach link context if admin initiated this call via dial button
        $context = Cache::pull("ziwo_call_context:{$p['callId']}");
        if ($context) {
            $log->update(['ziwo_metadata' => array_merge($log->ziwo_metadata ?? [], [
                'link_context' => $context,
            ])]);
        }
    }
}
