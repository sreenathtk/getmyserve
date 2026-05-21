<?php

namespace App\Listeners\Ziwo;

use App\Events\Ziwo\RecordingReady;
use App\Jobs\Ziwo\SyncCallRecording;
use App\Models\CallLog;
use App\Models\CallRecording;
use Illuminate\Contracts\Queue\ShouldQueue;

class DispatchRecordingSync implements ShouldQueue
{
    public string $queue = 'default';

    public function handle(RecordingReady $event): void
    {
        $p       = $event->payload;
        $callLog = CallLog::where('ziwo_call_id', $p['callId'])->first();

        if (! $callLog) return;

        $recording = CallRecording::updateOrCreate(
            ['ziwo_recording_id' => $p['recordingId']],
            [
                'call_log_id'       => $callLog->id,
                'recording_url'     => $p['recordingUrl'],
                'duration_seconds'  => $p['duration'] ?? null,
                'format'            => $p['format'] ?? 'mp3',
                'expires_at'        => now()->addDays(30),
            ]
        );

        SyncCallRecording::dispatch($recording->id);
    }
}
