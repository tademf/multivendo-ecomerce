<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VerificationRequest extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'verification_requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'status',
        'national_id',
        'national_id_image',
        'user_id',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'string',
        'reviewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Status constants.
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    /**
     * Get status options.
     *
     * @return array<string, string>
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
        ];
    }

    /**
     * Get human-readable status.
     *
     * @return string
     */
    public function getStatusTextAttribute(): string
    {
        return self::getStatusOptions()[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Check if request is pending.
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if request is approved.
     *
     * @return bool
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if request is rejected.
     *
     * @return bool
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Get the user who made the request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the admin who reviewed the request.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by', 'id');
    }

    /**
     * Scope to get pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope to get approved requests.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope to get rejected requests.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Scope to get requests for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Approve the verification request.
     */
    public function approve($reviewedBy = null, $notes = null): bool
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'reviewed_by' => $reviewedBy,
            'reviewed_at' => now(),
            'rejection_reason' => null,
        ]);

        // Also verify the user
        if ($this->user) {
            $this->user->update(['verified_at' => now()]);
        }

        return true;
    }

    /**
     * Reject the verification request.
     */
    public function reject($reviewedBy = null, $reason = null): bool
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'reviewed_by' => $reviewedBy,
            'reviewed_at' => now(),
            'rejection_reason' => $reason,
        ]);

        return true;
    }

    /**
     * Create a new verification request.
     */
    public static function createRequest($userId, $nationalId, $nationalIdImage = null): self
    {
        // Check if user already has a pending request
        $existing = self::where('user_id', $userId)
                       ->where('status', self::STATUS_PENDING)
                       ->first();

        if ($existing) {
            return $existing;
        }

        return self::create([
            'user_id' => $userId,
            'national_id' => $nationalId,
            'national_id_image' => $nationalIdImage,
            'status' => self::STATUS_PENDING,
        ]);
    }

    /**
     * Check if user has pending verification request.
     */
    public static function userHasPendingRequest($userId): bool
    {
        return self::where('user_id', $userId)
                  ->where('status', self::STATUS_PENDING)
                  ->exists();
    }

    /**
     * Get latest verification requests.
     */
    public static function getLatestRequests($limit = 20)
    {
        return self::with(['user' => function ($query) {
            $query->select('id', 'full_name', 'email', 'phone');
        }])
        ->latest()
        ->limit($limit)
        ->get();
    }

    /**
     * Get statistics for verification requests.
     */
    public static function getStatistics(): array
    {
        $total = self::count();
        $pending = self::pending()->count();
        $approved = self::approved()->count();
        $rejected = self::rejected()->count();

        return [
            'total' => $total,
            'pending' => $pending,
            'approved' => $approved,
            'rejected' => $rejected,
            'pending_percentage' => $total > 0 ? round(($pending / $total) * 100, 2) : 0,
            'approved_percentage' => $total > 0 ? round(($approved / $total) * 100, 2) : 0,
            'rejected_percentage' => $total > 0 ? round(($rejected / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Get the time elapsed since request was created.
     */
    public function getTimeElapsedAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get the time elapsed since request was reviewed.
     */
    public function getReviewTimeElapsedAttribute(): ?string
    {
        if (!$this->reviewed_at) {
            return null;
        }

        return $this->reviewed_at->diffForHumans();
    }
}