<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'password',
        'profile_picture',
        'account_number',
        'is_verified',
        'verified_at',
        'national_id',             // National ID NUMBER (string/number)
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_picture_url',
        'is_identity_verified',
        'has_pending_verification'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'verified_at' => 'datetime',
            'password' => 'hashed',
            'is_verified' => 'boolean',
        ];
    }

    // ============================================
    // VERIFICATION SYSTEM
    // ============================================
    
    public function verificationRequest()
    {
        return $this->hasOne(VerificationRequest::class);
    }

    public function isVerifiedVendor(): bool
    {
        return $this->verificationRequest && $this->verificationRequest->isApproved();
    }

    public function hasPendingVerification(): bool
    {
        return $this->verificationRequest && $this->verificationRequest->isPending();
    }

    /**
     * Get is_identity_verified attribute for frontend
     * This is for vendor-specific verification
     */
    public function getIsIdentityVerifiedAttribute(): bool
    {
        return $this->isVerifiedVendor();
    }

    /**
     * Get has_pending_verification attribute for frontend
     */
    public function getHasPendingVerificationAttribute(): bool
    {
        return $this->hasPendingVerification();
    }

    /**
     * Check if user is verified (general verification)
     * This checks the is_verified field in users table
     */
    public function isVerified(): bool
    {
        return $this->is_verified && !is_null($this->verified_at);
    }

    /**
     * Check if user can sell products
     */
    public function canSell(): bool
    {
        // This can be removed if you don't have vendor system
        return false;
    }

    // ============================================
    // PROFILE PICTURE
    // ============================================
    
    public function getProfilePictureUrlAttribute()
    {
        if (!$this->profile_picture) {
            return asset('images/default-avatar.png');
        }
        
        if (str_starts_with($this->profile_picture, 'http')) {
            return $this->profile_picture;
        }
        
        return asset('storage/' . $this->profile_picture);
    }

    // ============================================
    // ACCOUNT HELPERS
    // ============================================

    /**
     * Check if user has bank account set
     */
    public function hasBankAccount(): bool
    {
        return !empty($this->account_number);
    }

    /**
     * Get masked account number (for display)
     */
    public function getMaskedAccountNumberAttribute(): string
    {
        if (!$this->account_number) {
            return 'Not Set';
        }
        
        $length = strlen($this->account_number);
        if ($length <= 4) {
            return $this->account_number;
        }
        
        return substr($this->account_number, 0, 4) . str_repeat('*', $length - 4);
    }

    // ============================================
    // QUERY SCOPES
    // ============================================

    /**
     * Scope for verified users
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true)
                    ->whereNotNull('verified_at');
    }

    /**
     * Scope for users with national ID
     */
    public function scopeHasNationalId($query)
    {
        return $query->whereNotNull('national_id');
    }

    /**
     * Scope for users with bank account
     */
    public function scopeHasBankAccount($query)
    {
        return $query->whereNotNull('account_number');
    }

    // ============================================
    // STATISTICS & REPORTING
    // ============================================

    /**
     * Get user registration date in human-readable format
     */
    public function getRegistrationDateAttribute(): string
    {
        return $this->created_at->format('F j, Y');
    }

    /**
     * Get user age (based on registration)
     */
    public function getAccountAgeAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Check if user is new (registered within last 7 days)
     */
    public function getIsNewUserAttribute(): bool
    {
        return $this->created_at->gt(now()->subDays(7));
    }
}