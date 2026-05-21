<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActiveCall extends Model
{
    protected $fillable = [
        'ziwo_call_id', 'call_log_id', 'agent_id',
        'caller_number', 'callee_number',
        'status', 'started_at', 'last_updated_at', 'metadata',
    ];

    protected $casts = [
        'started_at'      => 'datetime',
        'last_updated_at' => 'datetime',
        'metadata'        => 'array',
    ];

    public function callLog(): BelongsTo
    {
        return $this->belongsTo(CallLog::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(CallAgent::class, 'agent_id');
    }
}
