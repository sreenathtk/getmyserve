<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnquiryStatusLog extends Model
{
    protected $fillable = ['enquiry_id', 'changed_by', 'old_status', 'new_status'];

    public function enquiry()
    {
        return $this->belongsTo(Enquiry::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function getLabelAttribute(): string
    {
        return match($this->new_status) {
            'pending'          => 'Pending',
            'in_progress'      => 'In Progress',
            'under_processing' => 'Under Processing',
            'completed'        => 'Completed',
            'resolved'         => 'Resolved',
            default            => ucfirst(str_replace('_', ' ', $this->new_status)),
        };
    }

    public function getBadgeClassAttribute(): string
    {
        return match($this->new_status) {
            'pending'          => 'bg-warning-subtle text-warning-emphasis',
            'in_progress'      => 'bg-info-subtle text-info-emphasis',
            'under_processing' => 'bg-primary-subtle text-primary-emphasis',
            'completed'        => 'badge-soft-teal',
            'resolved'         => 'bg-success-subtle text-success-emphasis',
            default            => 'bg-secondary-subtle text-secondary-emphasis',
        };
    }
}
