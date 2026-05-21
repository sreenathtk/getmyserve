<?php

namespace App\Listeners\Ziwo;

use App\Events\Ziwo\CallEnded;
use App\Models\CallLog;
use App\Models\CommunicationHistory;
use App\Models\CommunicationLink;
use App\Models\ServiceProvider;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateCommunicationHistory implements ShouldQueue
{
    public string $queue = 'default';

    public function handle(CallEnded $event): void
    {
        $p       = $event->payload;
        $callLog = CallLog::where('ziwo_call_id', $p['callId'])->with('agent')->first();

        if (! $callLog) return;

        $summary = sprintf(
            '%s call · %s → %s · %s · %s',
            ucfirst($callLog->direction),
            $callLog->caller_number,
            $callLog->callee_number,
            $callLog->duration_formatted,
            ucfirst($callLog->status)
        );

        $history = CommunicationHistory::create([
            'channel'        => 'call',
            'direction'      => $callLog->direction,
            'summary'        => $summary,
            'agent_id'       => $callLog->agent_id,
            'reference_type' => CallLog::class,
            'reference_id'   => $callLog->id,
            'happened_at'    => $callLog->started_at ?? now(),
            'metadata'       => [
                'status'           => $callLog->status,
                'duration_seconds' => $callLog->duration_seconds,
                'hangup_cause'     => $callLog->hangup_cause,
            ],
        ]);

        // Auto-link to matching entities by phone number
        $this->autoLink($history, $callLog->caller_number, $callLog->callee_number);

        // Also link from call context (if admin initiated via entity page)
        $context = $callLog->ziwo_metadata['link_context'] ?? null;
        if ($context && isset($context['linkable_type'], $context['linkable_id'])) {
            CommunicationLink::firstOrCreate([
                'communication_history_id' => $history->id,
                'linkable_type'            => $context['linkable_type'],
                'linkable_id'              => $context['linkable_id'],
            ]);
        }
    }

    private function autoLink(CommunicationHistory $history, string $caller, string $callee): void
    {
        foreach (array_filter([$caller, $callee]) as $phone) {
            $clean = preg_replace('/[^0-9]/', '', $phone);

            User::where('phone', 'LIKE', "%{$clean}%")
                ->orWhere('whatsapp_number', 'LIKE', "%{$clean}%")
                ->each(fn ($user) => $this->link($history, $user));

            ServiceProvider::where('comm_mobile', 'LIKE', "%{$clean}%")
                ->orWhere('whatsapp_number', 'LIKE', "%{$clean}%")
                ->each(fn ($sp) => $this->link($history, $sp));
        }
    }

    private function link(CommunicationHistory $history, mixed $model): void
    {
        CommunicationLink::firstOrCreate([
            'communication_history_id' => $history->id,
            'linkable_type'            => $model::class,
            'linkable_id'              => $model->id,
        ]);
    }
}
