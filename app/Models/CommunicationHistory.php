<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CommunicationHistory extends Model
{
    protected $fillable = [
        'channel', 'direction', 'summary', 'body',
        'agent_id', 'user_id',
        'reference_type', 'reference_id',
        'happened_at', 'metadata',
    ];

    protected $casts = [
        'happened_at' => 'datetime',
        'metadata'    => 'array',
    ];

    public function links(): HasMany
    {
        return $this->hasMany(CommunicationLink::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(CallAgent::class, 'agent_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getChannelIconAttribute(): string
    {
        return match ($this->channel) {
            'call'     => 'phone',
            'whatsapp' => 'whatsapp',
            'email'    => 'envelope',
            'note'     => 'sticky-note',
            default    => 'info-circle',
        };
    }

    public function getChannelColorAttribute(): string
    {
        return match ($this->channel) {
            'call'     => 'text-primary',
            'whatsapp' => 'text-success',
            'email'    => 'text-info',
            'note'     => 'text-warning',
            default    => 'text-secondary',
        };
    }
}
