<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CommunicationLink extends Model
{
    protected $fillable = [
        'communication_history_id', 'linkable_type', 'linkable_id',
    ];

    public function history(): BelongsTo
    {
        return $this->belongsTo(CommunicationHistory::class, 'communication_history_id');
    }

    public function linkable(): MorphTo
    {
        return $this->morphTo();
    }
}
