<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderFile extends Model
{
    protected $fillable = [
        'order_id',
        'file_name',
        'original_name',
        'file_path',
        'file_size',
        'mime_type',
        'uploaded_by',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getFormattedSizeAttribute(): string
    {
        $b = $this->file_size;
        if ($b >= 1048576) return round($b / 1048576, 1) . ' MB';
        if ($b >= 1024)    return round($b / 1024, 1) . ' KB';
        return $b . ' B';
    }

    public function getExtensionAttribute(): string
    {
        return strtolower(pathinfo($this->original_name, PATHINFO_EXTENSION));
    }

    public function getIconClassAttribute(): string
    {
        return match(true) {
            in_array($this->extension, ['pdf'])                          => 'ri-file-pdf-line text-danger',
            in_array($this->extension, ['doc', 'docx'])                  => 'ri-file-word-line text-primary',
            in_array($this->extension, ['xls', 'xlsx'])                  => 'ri-file-excel-line text-success',
            in_array($this->extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']) => 'ri-image-line text-info',
            in_array($this->extension, ['zip', 'rar', '7z'])             => 'ri-file-zip-line text-warning',
            default                                                      => 'ri-file-line text-muted',
        };
    }
}
