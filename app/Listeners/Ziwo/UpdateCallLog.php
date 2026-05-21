<?php

namespace App\Listeners\Ziwo;

use App\Events\Ziwo\CallAnswered;
use App\Events\Ziwo\CallEnded;
use App\Events\Ziwo\CallMissed;
use App\Events\Ziwo\CallTransferred;
use App\Models\ActiveCall;
use App\Models\CallAgent;
use App\Models\CallLog;
use App\Models\CallTransfer;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateCallLog implements ShouldQueue
{
    public string $queue = 'default';

    public function handleAnswered(CallAnswered $event): void
    {
        $p = $event->payload;

        CallLog::where('ziwo_call_id', $p['callId'])
               ->update(['status' => 'answered', 'answered_at' => now()]);

        ActiveCall::where('ziwo_call_id', $p['callId'])
                  ->update(['status' => 'answered', 'last_updated_at' => now()]);
    }

    public function handleEnded(CallEnded $event): void
    {
        $p        = $event->payload;
        $duration = $p['duration'] ?? 0;

        CallLog::where('ziwo_call_id', $p['callId'])->update([
            'status'                => 'ended',
            'ended_at'              => now(),
            'duration_seconds'      => $duration,
            'talk_duration_seconds' => $p['talkDuration'] ?? $duration,
            'hold_duration_seconds' => $p['holdDuration'] ?? 0,
            'hangup_cause'          => $p['hangupCause'] ?? null,
        ]);

        ActiveCall::where('ziwo_call_id', $p['callId'])->delete();
    }

    public function handleMissed(CallMissed $event): void
    {
        $p = $event->payload;

        CallLog::where('ziwo_call_id', $p['callId'])
               ->update(['status' => 'missed', 'ended_at' => now()]);

        ActiveCall::where('ziwo_call_id', $p['callId'])->delete();
    }

    public function handleTransferred(CallTransferred $event): void
    {
        $p   = $event->payload;
        $log = CallLog::where('ziwo_call_id', $p['callId'])->first();

        if (! $log) return;

        $log->update(['status' => 'transferred']);

        CallTransfer::create([
            'call_log_id'   => $log->id,
            'from_agent_id' => CallAgent::where('ziwo_agent_id', $p['fromAgent'] ?? null)->value('id'),
            'to_agent_id'   => CallAgent::where('ziwo_agent_id', $p['toAgent'] ?? null)->value('id'),
            'to_number'     => $p['toNumber'] ?? null,
            'transfer_type' => $p['transferType'] ?? 'blind',
            'status'        => 'completed',
            'initiated_at'  => now(),
            'completed_at'  => now(),
        ]);
    }
}
