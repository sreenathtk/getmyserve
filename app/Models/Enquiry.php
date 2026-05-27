<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    protected $fillable = [
        'user_id',
        'service_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'whatsapp',
        'location',
        'language',
        'remarks',
        'status',
        'assigned_sp_id',
        'quoted_price',
        'quoted_by',
        'quoted_at',
        'payment_status',
        'stripe_session_id',
        'stripe_payment_intent_id',
        'paid_amount',
        'paid_at',
        'refunded_amount',
    ];

    protected $casts = [
        'quoted_price'    => 'decimal:2',
        'paid_amount'     => 'decimal:2',
        'refunded_amount' => 'decimal:2',
        'quoted_at'       => 'datetime',
        'paid_at'         => 'datetime',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function files()
    {
        return $this->hasMany(EnquiryFile::class)->latest();
    }

    public function viewLogs()
    {
        return $this->hasMany(EnquiryViewLog::class)->latest();
    }

    public function statusLogs()
    {
        return $this->hasMany(EnquiryStatusLog::class)->latest();
    }

    public function assignedSp()
    {
        return $this->belongsTo(User::class, 'assigned_sp_id');
    }

    public function updates()
    {
        return $this->hasMany(EnquiryUpdate::class)->latest();
    }

    public function quotedBy()
    {
        return $this->belongsTo(User::class, 'quoted_by');
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function isQuoted(): bool
    {
        return $this->quoted_price !== null;
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isRefundable(): bool
    {
        return $this->isPaid()
            && $this->stripe_payment_intent_id !== null
            && $this->getRefundableAmount() > 0;
    }

    public function getRefundableAmount(): float
    {
        return max(0, (float) $this->paid_amount - (float) $this->refunded_amount);
    }
}
