<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Offer extends Model
{
    protected $fillable = [
        'offer_type',
        'title',
        'offer_price',
        'offer_detail',
        'button_text',
        'start_date',
        'end_date',
        'is_featured',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'start_date'  => 'date',
        'end_date'    => 'date',
        'is_featured' => 'boolean',
        'is_active'   => 'boolean',
        'offer_price' => 'decimal:2',
    ];

    public static array $offerTypeLabels = [
        'limited_offer' => 'Limited Offer',
        'best_value'    => 'Best Value',
        'flash_sale'    => 'Flash Sale',
        'exclusive'     => 'Exclusive',
        'seasonal'      => 'Seasonal',
    ];

    public function services()
    {
        return $this->belongsToMany(Service::class, 'offer_services')->withPivot('package');
    }

    // Backward-compat accessor for code that still reads $offer->service
    public function getServiceAttribute()
    {
        return $this->services->first();
    }

    public function scopeActive($query)
    {
        $today = Carbon::today();
        return $query->where('is_active', true)
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today);
    }

    public function getOfferTypeLabelAttribute(): string
    {
        return static::$offerTypeLabels[$this->offer_type] ?? $this->offer_type;
    }

    public function getIsCurrentlyActiveAttribute(): bool
    {
        $today = Carbon::today();
        return $this->is_active
            && $this->start_date <= $today
            && $this->end_date >= $today;
    }
}
