<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CallRecording extends Model
{
    protected $fillable = [
        'call_log_id', 'ziwo_recording_id', 'recording_url',
        'storage_path', 'storage_disk',
        'file_size_bytes', 'duration_seconds', 'format',
        'is_downloaded', 'downloaded_at', 'expires_at',
    ];

    protected $casts = [
        'is_downloaded' => 'boolean',
        'downloaded_at' => 'datetime',
        'expires_at'    => 'datetime',
    ];

    public function callLog(): BelongsTo
    {
        return $this->belongsTo(CallLog::class);
    }
}
