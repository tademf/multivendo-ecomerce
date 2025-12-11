<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'tag_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
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
     * Get the products that belong to the tag.
     * CORRECT: product_tag table connects products and tags
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,     // Related model
            'product_tag',      // Pivot table name
            'tag_id',           // Foreign key on pivot table for this model
            'product_id'        // Foreign key on pivot table for related model
        )->withTimestamps();
    }

    /**
     * Scope to search tags by name or description.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
    }

    /**
     * Get the count of products with this tag.
     */
    public function getProductsCountAttribute(): int
    {
        return $this->products()->count();
    }

    /**
     * Scope to get popular tags (most used).
     */
    public function scopePopular($query, $limit = 10)
    {
        return $query->withCount('products')
                    ->orderBy('products_count', 'desc')
                    ->limit($limit);
    }

    /**
     * Check if tag is being used by any product.
     */
    public function isInUse(): bool
    {
        return $this->products()->exists();
    }
}