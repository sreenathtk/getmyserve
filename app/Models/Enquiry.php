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

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
