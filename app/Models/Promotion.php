<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class Promotion extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'image',
        'video',
        'description',
        'payment_proof',
        'published_at',
        'expired_at',
        'status',
        'user_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'published_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    /**
     * ✅ CRITICAL FIX: Append accessors to JSON responses
     */
    protected $appends = [
        'image_url',
        'video_url',
        'payment_proof_url',
        'user_name',
        'formatted_published_date',
        'formatted_expiry_date',
        'duration_text',
        'payment_amount',
        'formatted_payment_amount',
        'remaining_days',
        'is_expiring_soon',
        'status_badge'
    ];

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_EXPIRED = 'expired';

    /**
     * Duration mapping for prices
     */
    const DURATION_PRICES = [
        '1' => 500,
        '7' => 1500,
        '14' => 2000,
        '21' => 2500,
        '30' => 3000
    ];

    const DURATION_TEXT = [
        '1' => '1 Day',
        '7' => '1 Week',
        '14' => '2 Weeks',
        '21' => '3 Weeks',
        '30' => '1 Month'
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scopes
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_APPROVED)
                     ->where('published_at', '<=', now())
                     ->where(function($q) {
                         $q->where('expired_at', '>=', now())
                           ->orWhereNull('expired_at');
                     });
    }

    public function scopeExpired($query)
    {
        return $query->where('status', self::STATUS_EXPIRED)
                     ->orWhere(function($q) {
                         $q->where('expired_at', '<', now())
                           ->where('status', '!=', self::STATUS_REJECTED);
                     });
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeFeatured($query)
    {
        return $query->approved()
                     ->active()
                     ->inRandomOrder()
                     ->limit(6);
    }

    /**
     * Check if promotion is active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_APPROVED && 
               ($this->published_at === null || $this->published_at <= now()) && 
               ($this->expired_at === null || $this->expired_at >= now());
    }

    /**
     * ✅ FIXED: Get image url
     */
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }
        
        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }
        
        // Check if file exists
        if (Storage::disk('public')->exists($this->image)) {
            return asset('storage/' . $this->image);
        }
        
        return null;
    }

    /**
     * ✅ FIXED: Get video url
     */
    public function getVideoUrlAttribute()
    {
        if (!$this->video) {
            return null;
        }
        
        if (str_starts_with($this->video, 'http')) {
            return $this->video;
        }
        
        // Check if file exists
        if (Storage::disk('public')->exists($this->video)) {
            return asset('storage/' . $this->video);
        }
        
        return null;
    }

    /**
     * Get payment proof url
     */
    public function getPaymentProofUrlAttribute()
    {
        if (!$this->payment_proof) {
            return null;
        }
        
        if (str_starts_with($this->payment_proof, 'http')) {
            return $this->payment_proof;
        }
        
        if (Storage::disk('public')->exists($this->payment_proof)) {
            return asset('storage/' . $this->payment_proof);
        }
        
        return null;
    }

    /**
     * Get user name
     */
    public function getUserNameAttribute()
    {
        return $this->user?->name ?? $this->user?->email ?? 'Unknown Vendor';
    }

    /**
     * Get formatted published date
     */
    public function getFormattedPublishedDateAttribute()
    {
        return $this->published_at?->format('F j, Y') ?? 'Not published';
    }

    /**
     * Get formatted expiry date
     */
    public function getFormattedExpiryDateAttribute()
    {
        return $this->expired_at?->format('F j, Y') ?? 'No expiry';
    }

    /**
     * Get duration text
     */
    public function getDurationTextAttribute()
    {
        if (!$this->published_at || !$this->expired_at) {
            return null;
        }
        
        $days = $this->published_at->diffInDays($this->expired_at);
        return self::DURATION_TEXT[(string)$days] ?? $days . ' Days';
    }

    /**
     * Get payment amount
     */
    public function getPaymentAmountAttribute()
    {
        if (!$this->published_at || !$this->expired_at) {
            return null;
        }
        
        $days = $this->published_at->diffInDays($this->expired_at);
        return self::DURATION_PRICES[(string)$days] ?? $days * 100;
    }

    /**
     * Get formatted payment amount
     */
    public function getFormattedPaymentAmountAttribute()
    {
        $amount = $this->payment_amount;
        return $amount ? number_format($amount, 0) . ' Birr' : null;
    }

    /**
     * Get remaining days
     */
    public function getRemainingDaysAttribute()
    {
        if (!$this->expired_at || !$this->isActive()) {
            return null;
        }
        return now()->diffInDays($this->expired_at, false);
    }

    /**
     * Check if expiring soon (within 3 days)
     */
    public function getIsExpiringSoonAttribute()
    {
        $days = $this->remaining_days;
        return $days !== null && $days <= 3;
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            self::STATUS_APPROVED => 'bg-green-100 text-green-800',
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800',
            self::STATUS_REJECTED => 'bg-red-100 text-red-800',
            self::STATUS_EXPIRED => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Calculate expiry date from duration
     */
    public static function calculateExpiryDate($duration, $startDate = null)
    {
        $start = $startDate ? Carbon::parse($startDate) : now();
        $days = (int)$duration;
        return $start->copy()->addDays($days);
    }
}