<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CallLog extends Model
{
    protected $fillable = [
        'ziwo_call_id', 'direction', 'status',
        'caller_number', 'callee_number', 'caller_name', 'callee_name',
        'agent_id', 'queue_name',
        'started_at', 'answered_at', 'ended_at',
        'duration_seconds', 'hold_duration_seconds', 'talk_duration_seconds',
        'hangup_cause', 'ziwo_metadata',
    ];

    protected $casts = [
        'started_at'    => 'datetime',
        'answered_at'   => 'datetime',
        'ended_at'      => 'datetime',
        'ziwo_metadata' => 'array',
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(CallAgent::class, 'agent_id');
    }

    public function recording(): HasOne
    {
        return $this->hasOne(CallRecording::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(CallNote::class);
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(CallTransfer::class);
    }

    public function activeCall(): HasOne
    {
        return $this->hasOne(ActiveCall::class);
    }

    public function communicationHistory(): HasOne
    {
        return $this->hasOne(CommunicationHistory::class, 'reference_id')
                    ->where('reference_type', self::class);
    }

    public function scopeInbound(Builder $q): Builder
    {
        return $q->where('direction', 'inbound');
    }

    public function scopeOutbound(Builder $q): Builder
    {
        return $q->where('direction', 'outbound');
    }

    public function scopeMissed(Builder $q): Builder
    {
        return $q->where('status', 'missed');
    }

    public function scopeToday(Builder $q): Builder
    {
        return $q->whereDate('started_at', today());
    }

    public function getDurationFormattedAttribute(): string
    {
        $s = $this->duration_seconds;
        return sprintf('%02d:%02d:%02d', intdiv($s, 3600), intdiv($s % 3600, 60), $s % 60);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'answered'    => 'bg-success',
            'missed'      => 'bg-danger',
            'initiated'   => 'bg-info',
            'transferred' => 'bg-warning',
            default       => 'bg-secondary',
        };
    }
}
