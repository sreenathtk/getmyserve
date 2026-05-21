<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'sub_category_id',
        'name',
        'description',
        'image',
        'icon',
        'service_type',
        'is_active',
        'has_packages',
        'has_premium_package',
        'basic_package_description',
        'basic_package_price',
        'basic_package_actual_price',
        'basic_package_discount_type',
        'basic_package_discount_value',
        'premium_package_description',
        'premium_package_price',
        'premium_package_actual_price',
        'premium_package_discount_type',
        'premium_package_discount_value',
        'estimate_value',
        'estimate_unit',
        'is_trending',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'is_trending'  => 'boolean',
        'has_packages' => 'boolean',
        'has_premium_package' => 'boolean',
        'basic_package_price' => 'decimal:2',
        'basic_package_actual_price' => 'decimal:2',
        'basic_package_discount_value' => 'decimal:2',
        'premium_package_price' => 'decimal:2',
        'premium_package_actual_price' => 'decimal:2',
        'premium_package_discount_value' => 'decimal:2',
    ];

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function serviceProviders()
    {
        return $this->belongsToMany(ServiceProvider::class, 'service_provider_services')
            ->withPivot(['turnaround_time', 'b2b_price', 'markup_percent', 'remarks', 'is_active'])
            ->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(ServiceReview::class);
    }

    public function activeReviews()
    {
        return $this->hasMany(ServiceReview::class)->where('is_active', true);
    }

    // Average rating from active reviews only (null if no reviews)
    public function getAverageRatingAttribute(): ?float
    {
        $avg = $this->activeReviews()->avg('rating');
        return $avg !== null ? round($avg, 1) : null;
    }

    public function getReviewCountAttribute(): int
    {
        return $this->activeReviews()->count();
    }
}
