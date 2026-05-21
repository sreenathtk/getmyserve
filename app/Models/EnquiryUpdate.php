<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnquiryUpdate extends Model
{
    protected $fillable = ['enquiry_id', 'user_id', 'note', 'status'];

    public function enquiry()
    {
        return $this->belongsTo(Enquiry::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending'          => 'Pending',
            'in_progress'      => 'In Progress',
            'under_processing' => 'Under Processing',
            'completed'        => 'Completed',
            'resolved'         => 'Resolved',
            default            => '',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'pending'          => 'bg-warning-subtle text-warning-emphasis',
            'in_progress'      => 'bg-info-subtle text-info-emphasis',
            'under_processing' => 'bg-primary-subtle text-primary-emphasis',
            'completed'        => 'badge-soft-teal',
            'resolved'         => 'bg-success-subtle text-success-emphasis',
            default            => 'bg-secondary-subtle text-secondary-emphasis',
        };
    }
}
