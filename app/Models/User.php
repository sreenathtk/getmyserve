<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Order;
use App\Models\ServiceReview;
use App\Models\StaffAssignment;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    const ROLE_ADMIN = 'admin';
    const ROLE_SERVICE_PROVIDER = 'service_provider';
    const ROLE_CUSTOMER = 'customer';
    const ROLE_STAFF = 'staff';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'whatsapp_number',
        'whatsapp_notifications',
        'country',
        'city',
        'address',
        'password',
        'role',
        'is_active',
        'last_login_at',
        'last_logout_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'      => 'datetime',
            'password'               => 'hashed',
            'is_active'              => 'boolean',
            'whatsapp_notifications' => 'boolean',
            'last_login_at'          => 'datetime',
            'last_logout_at'         => 'datetime',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isServiceProvider(): bool
    {
        return $this->role === self::ROLE_SERVICE_PROVIDER;
    }

    public function isCustomer(): bool
    {
        return $this->role === self::ROLE_CUSTOMER;
    }

    public function isStaff(): bool
    {
        return $this->role === self::ROLE_STAFF;
    }

    public function serviceProvider()
    {
        return $this->hasOne(ServiceProvider::class);
    }

    public function callAgent(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\CallAgent::class);
    }

    public function changeRequests()
    {
        return $this->hasMany(ServiceProviderChangeRequest::class, 'requested_by_user_id');
    }

    public function staffAssignments()
    {
        return $this->hasMany(StaffAssignment::class);
    }

    public function reviews()
    {
        return $this->hasMany(ServiceReview::class);
    }

    public function enquiries()
    {
        return $this->hasMany(Enquiry::class, 'user_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
