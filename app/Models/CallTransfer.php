<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CallTransfer extends Model
{
    protected $fillable = [
        'call_log_id', 'from_agent_id', 'to_agent_id',
        'to_number', 'transfer_type', 'status',
        'initiated_at', 'completed_at',
    ];

    protected $casts = [
        'initiated_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function callLog(): BelongsTo
    {
        return $this->belongsTo(CallLog::class);
    }

    public function fromAgent(): BelongsTo
    {
        return $this->belongsTo(CallAgent::class, 'from_agent_id');
    }

    public function toAgent(): BelongsTo
    {
        return $this->belongsTo(CallAgent::class, 'to_agent_id');
    }
}
