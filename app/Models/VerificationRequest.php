<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VerificationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'national_id',
        'national_id_image',
        'status',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at'
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    // Append these custom attributes to JSON/array output
    protected $appends = ['national_id_image_url', 'status_badge'];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Status check methods
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    // Status badge (HTML)
    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_PENDING => '<span class="badge bg-warning">Pending</span>',
            self::STATUS_APPROVED => '<span class="badge bg-success">Approved</span>',
            self::STATUS_REJECTED => '<span class="badge bg-danger">Rejected</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }

    // Status badge (text only for forms)
    public function getStatusTextAttribute()
    {
        return ucfirst($this->status);
    }

    // Get image URL
    public function getNationalIdImageUrlAttribute()
    {
        if (!$this->national_id_image) {
            return null;
        }
        
        if (str_starts_with($this->national_id_image, 'http')) {
            return $this->national_id_image;
        }
        
        return asset('storage/' . $this->national_id_image);
    }

    // Scope for filtering
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }
}