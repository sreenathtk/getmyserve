<?php

namespace App\Jobs\Ziwo;

use App\Models\CallRecording;
use App\Services\Ziwo\ZiwoRecordingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SyncCallRecording implements ShouldQueue
{
    use Queueable;

    public int   $tries  = 3;
    public array $backoff = [60, 300];

    public function __construct(private readonly int $recordingId)
    {
        $this->onQueue('recordings');
    }

    public function handle(ZiwoRecordingService $service): void
    {
        $recording = CallRecording::with('callLog')->find($this->recordingId);

        if (! $recording || $recording->is_downloaded) {
            return;
        }

        $service->downloadAndStore($recording);
    }

    public function failed(\Throwable $e): void
    {
        Log::channel('ziwo')->error('SyncCallRecording permanently failed.', [
            'recording_id' => $this->recordingId,
            'error'        => $e->getMessage(),
        ]);
    }
}
