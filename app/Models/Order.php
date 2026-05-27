<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Explicit imports satisfy static analysis — same namespace but Intelephense requires them
use App\Models\OrderFile;
use App\Models\OrderItem;
use App\Models\Payment;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'assigned_staff_id',
        'assigned_sp_id',
        'stripe_session_id',
        'stripe_payment_intent_id',
        'status',
        'amount',
        'discount_amount',
        'vat_rate',
        'vat_amount',
        'offer_snapshot',
        'currency',
        'items',
        'notes',
        'refund_status',
        'refunded_amount',
    ];

    protected $casts = [
        'items'           => 'array',
        'offer_snapshot'  => 'array',
        'amount'          => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'vat_rate'        => 'decimal:2',
        'vat_amount'      => 'decimal:2',
        'refunded_amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_staff_id');
    }

    public function assignedSp(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_sp_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(OrderFile::class)->latest();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->latest();
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'paid'             => 'badge-soft-success',
            'processing'       => 'badge-soft-info',
            'completed'        => 'badge-soft-teal',
            'cancel_requested' => 'badge-soft-orange',
            'cancelled'        => 'badge-soft-danger',
            'failed'           => 'badge-soft-danger',
            default            => 'badge-soft-warning', // pending
        };
    }

    public function isCancellationRequested(): bool
    {
        return $this->status === 'cancel_requested';
    }

    public function getRefundStatusBadgeClass(): string
    {
        return match($this->refund_status) {
            'requested' => 'badge-soft-warning',
            'partial'   => 'badge-soft-info',
            'full'      => 'badge-soft-danger',
            default     => 'badge-soft-secondary',
        };
    }

    public function getRefundableAmount(): float
    {
        return max(0, (float) $this->amount - (float) $this->refunded_amount);
    }

    public function isRefundable(): bool
    {
        return $this->stripe_payment_intent_id !== null
            && $this->refund_status !== 'full'
            && $this->getRefundableAmount() > 0;
    }
}
