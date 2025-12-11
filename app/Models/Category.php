<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'category_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description', // ADD THIS - exists in table!
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
     * Get the products for the category.
     */
    public function products()
    {
        // Since Product model uses 'category_id' as foreign key
        // and Category uses 'category_id' as primary key
        // This should work correctly
        return $this->hasMany(Product::class, 'category_id', 'category_id');
    }

    /**
     * Scope to get categories with products.
     */
    public function scopeWithProducts($query)
    {
        return $query->with(['products' => function ($query) {
            $query->select('product_id', 'name', 'price', 'category_id');
        }]);
    }

    /**
     * Scope to search categories by name.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
    }

    /**
     * Get the total products count in this category.
     */
    public function getProductsCountAttribute(): int
    {
        return $this->products()->count();
    }
}