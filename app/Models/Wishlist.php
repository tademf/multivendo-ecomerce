<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wishlist extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wishlist';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'product_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the wishlist item.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the product that is wishlisted.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    /**
     * Scope to get wishlist items for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to check if a product is in user's wishlist.
     */
    public function scopeExistsForUser($query, $userId, $productId)
    {
        return $query->where('user_id', $userId)
                    ->where('product_id', $productId);
    }

    /**
     * Add a product to user's wishlist.
     */
    public static function addToWishlist($userId, $productId): bool
    {
        if (self::where('user_id', $userId)->where('product_id', $productId)->exists()) {
            return false; // Already in wishlist
        }

        return self::create([
            'user_id' => $userId,
            'product_id' => $productId,
        ]) !== null;
    }

    /**
     * Remove a product from user's wishlist.
     */
    public static function removeFromWishlist($userId, $productId): bool
    {
        return self::where('user_id', $userId)
                  ->where('product_id', $productId)
                  ->delete() > 0;
    }

    /**
     * Toggle wishlist status for a product.
     */
    public static function toggleWishlist($userId, $productId): bool
    {
        $exists = self::where('user_id', $userId)
                     ->where('product_id', $productId)
                     ->exists();

        if ($exists) {
            return self::removeFromWishlist($userId, $productId);
        } else {
            return self::addToWishlist($userId, $productId);
        }
    }

    /**
     * Get user's wishlist with product details.
     */
    public static function getUserWishlist($userId)
    {
        return self::with(['product' => function ($query) {
            $query->select('product_id', 'name', 'price', 'image');
        }])
        ->where('user_id', $userId)
        ->latest()
        ->get();
    }

    /**
     * Count user's wishlist items.
     */
    public static function countUserWishlist($userId): int
    {
        return self::where('user_id', $userId)->count();
    }

    /**
     * Clear all items from user's wishlist.
     */
    public static function clearUserWishlist($userId): bool
    {
        return self::where('user_id', $userId)->delete() > 0;
    }
}