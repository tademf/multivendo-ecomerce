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
         'is_verified'
        
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
            'password' => 'hashed',
        ];
    }

    /**
     * ROLE HELPERS
     */
    
    public function isVendor(): bool
    {
        return $this->role === 'vendor';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function scopeVendors($query)
    {
        return $query->where('role', 'vendor');
    }

    public function scopeCustomers($query)
    {
        return $query->where('role', 'customer');
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * VERIFICATION SYSTEM
     */
    
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
     * Check if user can sell products
     */
    public function canSell(): bool
    {
        return $this->isVendor() && $this->isVerifiedVendor();
    }

    /**
     * PROFILE PICTURE
     */
    
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

    /**
     * PRODUCTS & WISHLIST
     */
    
    public function products()
    {
        return $this->hasMany(Product::class, 'user_id');
    }

    public function wishlist()
    {
        return $this->belongsToMany(
            Product::class,
            'wishlist',
            'user_id',
            'product_id'
        )->withTimestamps();
    }

    public function hasInWishlist($productId): bool
    {
        return $this->wishlist()->where('product_id', $productId)->exists();
    }

    /**
     * STOCK HISTORY
     */
    
    public function stockHistories()
    {
        return $this->hasMany(StockHistory::class, 'user_id');
    }

    /**
     * EMAIL VERIFICATION (from Laravel)
     */
    
    public function isEmailVerified(): bool
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * BAN/STATUS SYSTEM (optional - add if needed)
     */
    
    public function isBanned(): bool
    {
        // If you add a 'banned_at' column
        // return !is_null($this->banned_at);
        return false;
    }

    public function isActive(): bool
    {
        return !$this->isBanned();
    }

    /**
     * NOTIFICATIONS PREFERENCES (optional)
     */
    
    public function shouldReceiveEmailNotifications(): bool
    {
        // If you add notification preferences
        // return $this->email_notifications === true;
        return true;
    }

    /**
     * USER STATISTICS (optional)
     */
    
    public function getProductsCountAttribute(): int
    {
        return $this->products()->count();
    }

    public function getWishlistCountAttribute(): int
    {
        return $this->wishlist()->count();
    }

    /**
     * Get user's initials for avatar placeholder
     */
    public function getInitialsAttribute(): string
    {
        $names = explode(' ', $this->full_name);
        $initials = '';
        
        foreach ($names as $name) {
            if (!empty($name)) {
                $initials .= strtoupper($name[0]);
                if (strlen($initials) >= 2) break;
            }
        }
        
        return $initials ?: strtoupper(substr($this->email, 0, 2));
    }
}