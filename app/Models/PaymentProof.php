<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentProof extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'image_path',
        'original_name',
        'file_type',
        'file_size',
        'description',
    ];

    /**
     * Get the payment that owns the proof
     */
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Get file size in human readable format
     */
    public function getFormattedFileSizeAttribute()
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Get full image URL
     */
    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->image_path);
    }
}