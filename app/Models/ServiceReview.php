<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceReview extends Model
{
    protected $fillable = [
        'service_id',
        'user_id',
        'rating',
        'comment',
        'attachments',
        'is_active',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'rating'      => 'integer',
        'attachments' => 'array',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
