<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnquiryViewLog extends Model
{
    protected $fillable = ['enquiry_id', 'user_id'];

    public function enquiry()
    {
        return $this->belongsTo(Enquiry::class);
    }

    public function viewer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
