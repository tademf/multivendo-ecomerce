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
        'national_id',
        'profile_picture',
        'verified_at',
        // REMOVE: 'category_id', 'tag_id', 'product_id' - NOT in table!
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'verified_at' => 'datetime',
        ];
    }

    /**
     * Get the products for the user (seller).
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'user_id');
    }

    /**
     * Get the user's wishlist items.
     */
    public function wishlist()
    {
        return $this->belongsToMany(
            Product::class,
            'wishlist',
            'user_id',
            'product_id'
        )->withTimestamps();
    }

    /**
     * Check if product is in user's wishlist.
     */
    public function hasInWishlist($productId): bool
    {
        return $this->wishlist()->where('product_id', $productId)->exists();
    }

    /**
     * Get the verification requests for the user.
     */
    public function verificationRequests()
    {
        return $this->hasMany(VerificationRequest::class, 'user_id');
    }

    /**
     * Get the stock histories created by the user.
     */
    public function stockHistories()
    {
        return $this->hasMany(StockHistory::class, 'user_id');
    }

    /**
     * Check if user is verified.
     */
    public function isVerified(): bool
    {
        return !is_null($this->verified_at);
    }

    /**
     * Scope to get only verified users.
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('verified_at');
    }

    /**
     * Scope to get only unverified users.
     */
    public function scopeUnverified($query)
    {
        return $query->whereNull('verified_at');
    }
}