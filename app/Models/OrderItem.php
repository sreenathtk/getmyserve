<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    const STATUS_ACTIVE    = 'active';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'order_id',
        'service_id',
        'service_name',
        'package',
        'price',
        'original_price',
        'combo_offer_id',
        'status',
        'combo_offer_title',
        'combo_offer_price',
    ];

    protected $casts = [
        'price'             => 'decimal:2',
        'original_price'    => 'decimal:2',
        'combo_offer_price' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function isCombo(): bool
    {
        return (bool) $this->combo_offer_id;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * The refund value this item represents when cancelled.
     * For a combo item, the full combo_offer_price is refunded together with its siblings.
     * This method returns the individual item's share only for non-combo items.
     */
    public function individualRefundAmount(): float
    {
        return (float) ($this->price ?? 0);
    }
}
