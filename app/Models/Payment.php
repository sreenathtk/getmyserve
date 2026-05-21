<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'stripe_payment_intent_id',
        'stripe_refund_id',
        'type',
        'amount',
        'currency',
        'status',
        'metadata',
    ];

    protected $casts = [
        'amount'   => 'decimal:2',
        'metadata' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function getTypeLabel(): string
    {
        return match($this->type) {
            'charge' => 'Payment',
            'refund' => 'Refund',
            default  => ucfirst($this->type),
        };
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'succeeded' => 'badge-soft-success',
            'pending'   => 'badge-soft-warning',
            'failed'    => 'badge-soft-danger',
            'canceled'  => 'badge-soft-secondary',
            default     => 'badge-soft-secondary',
        };
    }
}
