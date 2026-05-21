<?php

namespace App\Services\Ziwo;

use App\Models\CallRecording;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ZiwoRecordingService
{
    public function __construct(private ZiwoApiClient $client) {}

    public function getRecording(string $ziwoRecordingId): array
    {
        return $this->client->get("/recordings/{$ziwoRecordingId}");
    }

    public function downloadAndStore(CallRecording $recording): bool
    {
        try {
            $response = Http::timeout(120)->get($recording->recording_url);

            if (! $response->successful()) {
                Log::channel('ziwo')->error('Recording download failed.', [
                    'recording_id' => $recording->id,
                    'status'       => $response->status(),
                ]);
                return false;
            }

            $disk = config('ziwo.recording_disk', 'local');
            $path = "call-recordings/{$recording->callLog->ziwo_call_id}/{$recording->ziwo_recording_id}.{$recording->format}";

            Storage::disk($disk)->put($path, $response->body());

            $recording->update([
                'storage_path'    => $path,
                'storage_disk'    => $disk,
                'file_size_bytes' => strlen($response->body()),
                'is_downloaded'   => true,
                'downloaded_at'   => now(),
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::channel('ziwo')->error('Recording download exception.', [
                'recording_id' => $recording->id,
                'error'        => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function getPlaybackUrl(CallRecording $recording): string
    {
        if ($recording->is_downloaded && $recording->storage_path) {
            return route('admin.calls.recording.stream', $recording->call_log_id);
        }

        return $recording->recording_url;
    }
}
