<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WatiTemplate extends Model
{
    protected $fillable = [
        'template_key',
        'element_name',
        'description',
        'recipient_type',
        'body',
        'category',
        'language',
        'status',
        'rejection_reason',
        'last_synced_at',
    ];

    protected $casts = [
        'last_synced_at' => 'datetime',
    ];

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'APPROVED'  => 'badge-soft-success',
            'PENDING'   => 'badge-soft-warning',
            'REJECTED'  => 'badge-soft-danger',
            'PAUSED'    => 'badge-soft-warning',
            'DISABLED'  => 'badge-soft-secondary',
            default     => 'badge-soft-secondary',
        };
    }

    public function statusIcon(): string
    {
        return match ($this->status) {
            'APPROVED'  => 'ri-checkbox-circle-line',
            'PENDING'   => 'ri-time-line',
            'REJECTED'  => 'ri-close-circle-line',
            'PAUSED'    => 'ri-pause-circle-line',
            'DISABLED'  => 'ri-forbid-line',
            default     => 'ri-question-line',
        };
    }
}
