<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'product_id';
    
    protected $fillable = [
        'name',
        'reference',
        'description',
        'product_type',
        'price',
        'price_tiers',
        'stock',
        'image',
        'additional_image',
        'status',
        'sold_count',
        'user_id',
        'category_id',
    ];
    
    protected $casts = [
        'price' => 'decimal:2',
        'price_tiers' => 'json',
        'stock' => 'integer',
        'sold_count' => 'integer',
    ];
    
    protected $appends = [
        'final_price',
        'stock_status',
        'main_image_url',
        'price_tiers_array',
        'all_images',
        'selected_additional_image'
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($product) {
            if (empty($product->reference)) {
                $product->reference = 'PROD-' . strtoupper(Str::random(8));
            }
            
            if ($product->product_type === 'onstock' && empty($product->price_tiers)) {
                $product->price_tiers = json_encode([
                    ['price' => 50, 'stock' => 10]
                ]);
            }
        });
        
        static::updating(function ($product) {
            if ($product->stock <= 0 && $product->product_type === 'onstock') {
                $product->status = 'out_of_stock';
            } elseif ($product->status === 'out_of_stock' && $product->stock > 0) {
                $product->status = 'active';
            }
            
            if ($product->product_type === 'onstock' && is_array($product->price_tiers)) {
                $product->price_tiers = json_encode($product->price_tiers);
            }
        });
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }
    
    public function tags()
    {
        return $this->belongsToMany(
            Tag::class, 
            'product_tag', 
            'product_id', 
            'tag_id',
            'product_id',
            'tag_id'
        )->withTimestamps();
    }
    
    // Additional images relationship
    public function additionalImages()
    {
        return $this->hasMany(AdditionalImage::class, 'product_id', 'product_id')
                    ->orderBy('display_order')
                    ->orderBy('created_at');
    }
    
    // NEW: Discount relationship
    public function discount()
    {
        return $this->hasOne(Discount::class, 'product_id', 'product_id')
                    ->where('status', 'active')
                    ->where('end_date', '>', Carbon::now());
    }
    
    // NEW: All discounts (including inactive/expired)
    public function discounts()
    {
        return $this->hasMany(Discount::class, 'product_id', 'product_id');
    }
    
    // NEW: Cart relationship
    public function carts()
    {
        return $this->hasMany(Cart::class, 'product_id', 'product_id');
    }
    
    // NEW: Wishlist relationship
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class, 'product_id', 'product_id');
    }
    
    // NEW: Accessor to check if product has active discount
    public function getHasActiveDiscountAttribute()
    {
        return $this->discount !== null;
    }
    
    // NEW: Accessor to get discounted price
    public function getDiscountedPriceAttribute()
    {
        if (!$this->has_active_discount) {
            return $this->price;
        }
        
        $discountPercent = $this->discount->discount_amount;
        $originalPrice = (float) $this->price;
        $discountedPrice = $originalPrice - ($originalPrice * $discountPercent / 100);
        
        return number_format($discountedPrice, 2, '.', '');
    }
    
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }
    
    public function scopeOutOfStock($query)
    {
        return $query->where('stock', '<=', 0);
    }
    
    public function scopeByType($query, $type)
    {
        return $query->where('product_type', $type);
    }
    
    // NEW: Scope for products with active discounts
    public function scopeWithActiveDiscounts($query)
    {
        return $query->whereHas('discount', function($q) {
            $q->where('status', 'active')
              ->where('end_date', '>', Carbon::now());
        });
    }
    
    public function getFinalPriceAttribute()
    {
        return $this->price;
    }
    
    public function getStockStatusAttribute()
    {
        if ($this->stock <= 0) {
            return 'out_of_stock';
        }
        return 'in_stock';
    }
    
    public function getMainImageUrlAttribute()
    {
        if (!$this->image) {
            return asset('images/default-product.png');
        }
        
        if (Str::startsWith($this->image, ['http://', 'https://'])) {
            return $this->image;
        }
        
        return asset('storage/' . $this->image);
    }
    
    public function getPriceTiersArrayAttribute()
    {
        if (empty($this->price_tiers)) {
            return [];
        }
        
        if (is_array($this->price_tiers)) {
            return $this->price_tiers;
        }
        
        if (is_string($this->price_tiers)) {
            try {
                return json_decode($this->price_tiers, true) ?? [];
            } catch (\Exception $e) {
                return [];
            }
        }
        
        return [];
    }
    
    // Get all images including main image
    public function getAllImagesAttribute()
    {
        $images = collect();
        
        // Add main image
        if ($this->image) {
            $images->push([
                'id' => null,
                'image_path' => $this->image,
                'is_main' => true,
                'image_url' => $this->main_image_url,
                'display_order' => 0
            ]);
        }
        
        // Add additional images
        $additionalImages = $this->additionalImages->map(function ($image) {
            return [
                'id' => $image->additional_image_id,
                'image_path' => $image->image_path,
                'is_main' => false,
                'image_url' => $image->image_url,
                'display_order' => $image->display_order,
                'is_selected' => $image->is_selected
            ];
        });
        
        return $images->merge($additionalImages)->sortBy('display_order')->values();
    }
    
    // Get selected additional image for purchase
    public function getSelectedAdditionalImageAttribute()
    {
        return $this->additionalImages()->selected()->first();
    }
    
    public function getPriceForQuantity($quantity)
    {
        if ($this->product_type !== 'onstock' || empty($this->price_tiers_array)) {
            return $this->price;
        }
        
        $quantity = max(1, $quantity);
        
        foreach ($this->price_tiers_array as $tier) {
            if (isset($tier['price']) && isset($tier['stock'])) {
                return $tier['price'];
            }
        }
        
        return $this->price;
    }
    
    public function isAvailable($quantity = 1)
    {
        if ($this->product_type === 'one-time') {
            return true;
        }
        
        return $this->stock >= $quantity;
    }
    
    public function updateStock($quantity, $type = 'sale')
    {
        if ($this->product_type !== 'onstock') {
            return true;
        }
        
        if ($type === 'sale' || $type === 'deduct') {
            $this->decrement('stock', $quantity);
            $this->increment('sold_count', $quantity);
        } elseif ($type === 'restock' || $type === 'add') {
            $this->increment('stock', $quantity);
        }
        
        if ($this->stock <= 0) {
            $this->status = 'out_of_stock';
        } elseif ($this->status === 'out_of_stock' && $this->stock > 0) {
            $this->status = 'active';
        }
        
        return $this->save();
    }
    
    public function setPriceTiersAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['price_tiers'] = json_encode($value);
        } elseif (is_string($value)) {
            $this->attributes['price_tiers'] = $value;
        } else {
            $this->attributes['price_tiers'] = json_encode([]);
        }
    }
    
    public function getPriceTiersAttribute($value)
    {
        if (empty($value)) {
            return [];
        }
        
        if (is_array($value)) {
            return $value;
        }
        
        try {
            return json_decode($value, true) ?? [];
        } catch (\Exception $e) {
            return [];
        }
    }
    
    public function getProductTypeLabelAttribute()
    {
        return $this->product_type === 'onstock' ? 'On-Stock' : 'One-Time';
    }
    
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'active' => 'Active',
            'inactive' => 'Inactive',
            'out_of_stock' => 'Out of Stock',
            'draft' => 'Draft',
            default => ucfirst($this->status)
        };
    }
    
    // NEW: Accessor to get discount percentage
    public function getDiscountPercentageAttribute()
    {
        if (!$this->has_active_discount) {
            return 0;
        }
        return $this->discount->discount_amount;
    }
    
    // NEW: Accessor to get discount name
    public function getDiscountNameAttribute()
    {
        if (!$this->has_active_discount) {
            return null;
        }
        return $this->discount->discount_name;
    }
    
    // NEW: Accessor to get discount end date
    public function getDiscountEndDateAttribute()
    {
        if (!$this->has_active_discount) {
            return null;
        }
        return $this->discount->end_date;
    }
}