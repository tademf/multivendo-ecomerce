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
        'minimum_stock',
        'needs_restock',
        'image',
        'additional_images',
        'status',
        'is_featured',
        'is_best_seller',
        'is_new_arrival',
        'discount_price',
        'discount_start',
        'discount_end',
        'discount_percentage',
        'slug',
        'meta_title',
        'meta_description',
        'weight',
        'length',
        'width',
        'height',
        'specifications',
        'has_variants',
        'variant_options',
        'view_count',
        'sold_count',
        'wishlist_count',
        'shipping_weight',
        'requires_shipping',
        'shipping_cost',
        'free_shipping',
        'tax_rate',
        'tax_class',
        'is_taxable',
        'manage_stock',
        'allow_backorders',
        'backorder_limit',
        'low_stock_threshold',
        'user_id',
        'category_id',
    ];
    
    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'price_tiers' => 'json', // Changed from 'array' to 'json'
        'additional_images' => 'array',
        'specifications' => 'array',
        'variant_options' => 'array',
        'needs_restock' => 'boolean',
        'is_featured' => 'boolean',
        'is_best_seller' => 'boolean',
        'is_new_arrival' => 'boolean',
        'has_variants' => 'boolean',
        'requires_shipping' => 'boolean',
        'free_shipping' => 'boolean',
        'is_taxable' => 'boolean',
        'manage_stock' => 'boolean',
        'allow_backorders' => 'boolean',
        'stock' => 'integer',
        'minimum_stock' => 'integer',
        'view_count' => 'integer',
        'sold_count' => 'integer',
        'wishlist_count' => 'integer',
        'backorder_limit' => 'integer',
        'low_stock_threshold' => 'integer',
        'discount_start' => 'date',
        'discount_end' => 'date',
        'discount_percentage' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'weight' => 'decimal:2',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'shipping_weight' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
    ];
    
    protected $appends = [
        'final_price',
        'discount_active',
        'stock_status',
        'main_image_url',
        'gallery_images',
        'is_low_stock',
        'is_out_of_stock',
        'tax_amount',
        'formatted_price',
        'discounted_amount',
        'discount_percentage_calculated',
        'price_tiers_array' // Added this to ensure proper JSON handling
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($product) {
            // Auto-generate slug if not provided
            if (empty($product->slug)) {
                $baseSlug = Str::slug($product->name);
                $slug = $baseSlug;
                $counter = 1;
                
                // Check if slug already exists
                while (self::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }
                
                $product->slug = $slug;
            }
            
            // Auto-generate reference if not provided
            if (empty($product->reference)) {
                $product->reference = 'PROD-' . strtoupper(Str::random(8));
            }
            
            // Ensure price_tiers is properly set for onstock products
            if ($product->product_type === 'onstock' && empty($product->price_tiers)) {
                $product->price_tiers = json_encode([
                    ['min_quantity' => 1, 'max_quantity' => 10, 'price' => $product->price],
                    ['min_quantity' => 11, 'max_quantity' => 50, 'price' => $product->price * 0.9],
                ]);
            }
        });
        
        static::updating(function ($product) {
            // Check stock levels and update needs_restock
            if ($product->stock <= $product->low_stock_threshold) {
                $product->needs_restock = true;
            } else {
                $product->needs_restock = false;
            }
            
            // Update status based on stock
            if ($product->stock <= 0 && $product->manage_stock) {
                $product->status = 'out_of_stock';
            } elseif ($product->status === 'out_of_stock' && $product->stock > 0) {
                $product->status = 'active';
            }
            
            // Ensure price_tiers is properly formatted
            if ($product->product_type === 'onstock' && is_array($product->price_tiers)) {
                $product->price_tiers = json_encode($product->price_tiers);
            }
        });
        
        // Handle price_tiers JSON decoding when retrieving
        static::retrieved(function ($product) {
            if (!empty($product->price_tiers) && is_string($product->price_tiers)) {
                try {
                    $product->price_tiers = json_decode($product->price_tiers, true);
                } catch (\Exception $e) {
                    $product->price_tiers = [];
                }
            }
        });
    }
    
    // Relationships
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
        // Updated to use the correct primary key name
        return $this->belongsToMany(
            Tag::class, 
            'product_tag', 
            'product_id', 
            'tag_id',
            'product_id',
            'tag_id' // Assuming tags table has 'id' as primary key
        )->withTimestamps();
    }
    
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class, 'product_id', 'product_id');
    }
    
    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
    
    public function scopeBestSeller($query)
    {
        return $query->where('is_best_seller', true);
    }
    
    public function scopeNewArrival($query)
    {
        return $query->where('is_new_arrival', true);
    }
    
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0)->orWhere('allow_backorders', true);
    }
    
    public function scopeLowStock($query)
    {
        return $query->where('stock', '<=', $this->low_stock_threshold)
                     ->where('stock', '>', 0);
    }
    
    public function scopeOutOfStock($query)
    {
        return $query->where('stock', '<=', 0)
                     ->where('manage_stock', true);
    }
    
    public function scopeOnDiscount($query)
    {
        $now = Carbon::now();
        return $query->whereNotNull('discount_price')
                     ->where(function($q) use ($now) {
                         $q->whereNull('discount_start')
                           ->orWhere('discount_start', '<=', $now);
                     })
                     ->where(function($q) use ($now) {
                         $q->whereNull('discount_end')
                           ->orWhere('discount_end', '>=', $now);
                     });
    }
    
    public function scopeByType($query, $type)
    {
        return $query->where('product_type', $type);
    }
    
    // Accessors
    public function getFinalPriceAttribute()
    {
        if ($this->isDiscountActive()) {
            if ($this->discount_price) {
                return $this->discount_price;
            } elseif ($this->discount_percentage) {
                return $this->price - ($this->price * $this->discount_percentage / 100);
            }
        }
        return $this->price;
    }
    
    public function getDiscountActiveAttribute()
    {
        return $this->isDiscountActive();
    }
    
    public function getStockStatusAttribute()
    {
        if ($this->stock <= 0 && $this->manage_stock) {
            return 'out_of_stock';
        } elseif ($this->stock <= $this->low_stock_threshold) {
            return 'low_stock';
        } elseif ($this->stock <= $this->minimum_stock) {
            return 'minimum_stock';
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
    
    public function getGalleryImagesAttribute()
    {
        $images = [];
        
        // Add main image
        if ($this->image) {
            $images[] = $this->main_image_url;
        }
        
        // Add additional images
        if ($this->additional_images && is_array($this->additional_images)) {
            foreach ($this->additional_images as $image) {
                if (Str::startsWith($image, ['http://', 'https://'])) {
                    $images[] = $image;
                } else {
                    $images[] = asset('storage/' . $image);
                }
            }
        }
        
        return $images;
    }
    
    public function getIsLowStockAttribute()
    {
        return $this->stock <= $this->low_stock_threshold && $this->stock > 0;
    }
    
    public function getIsOutOfStockAttribute()
    {
        return $this->stock <= 0 && $this->manage_stock;
    }
    
    public function getTaxAmountAttribute()
    {
        if (!$this->is_taxable || $this->tax_rate <= 0) {
            return 0;
        }
        
        return ($this->final_price * $this->tax_rate) / 100;
    }
    
    public function getFormattedPriceAttribute()
    {
        return number_format($this->final_price, 2);
    }
    
    public function getDiscountedAmountAttribute()
    {
        if (!$this->isDiscountActive()) {
            return 0;
        }
        
        return $this->price - $this->final_price;
    }
    
    public function getDiscountPercentageCalculatedAttribute()
    {
        if (!$this->isDiscountActive() || $this->price <= 0) {
            return 0;
        }
        
        return round((($this->price - $this->final_price) / $this->price) * 100, 2);
    }
    
    // NEW ACCESSOR: Ensure price_tiers is always returned as array
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
    
    // For onstock products - get price based on quantity
    public function getPriceForQuantity($quantity)
    {
        if ($this->product_type !== 'onstock' || empty($this->price_tiers_array)) {
            return $this->final_price;
        }
        
        $quantity = max(1, $quantity);
        
        foreach ($this->price_tiers_array as $tier) {
            if (isset($tier['min_quantity']) && isset($tier['price']) &&
                $quantity >= $tier['min_quantity'] && 
                (empty($tier['max_quantity']) || $quantity <= $tier['max_quantity'])) {
                return $tier['price'];
            }
        }
        
        // Return last tier price or base price
        $lastTier = end($this->price_tiers_array);
        return $lastTier['price'] ?? $this->final_price;
    }
    
    // Check if discount is active
    public function isDiscountActive()
    {
        if (!$this->discount_price && !$this->discount_percentage) {
            return false;
        }
        
        $now = Carbon::now();
        
        if ($this->discount_start && $now->lt($this->discount_start)) {
            return false;
        }
        
        if ($this->discount_end && $now->gt($this->discount_end)) {
            return false;
        }
        
        return true;
    }
    
    // Check stock availability
    public function isAvailable($quantity = 1)
    {
        if (!$this->manage_stock) {
            return true;
        }
        
        if ($this->stock >= $quantity) {
            return true;
        }
        
        if ($this->allow_backorders && (!$this->backorder_limit || $quantity <= $this->backorder_limit)) {
            return true;
        }
        
        return false;
    }
    
    // Update stock
    public function updateStock($quantity, $type = 'sale')
    {
        if (!$this->manage_stock) {
            return true;
        }
        
        if ($type === 'sale' || $type === 'deduct') {
            $this->decrement('stock', $quantity);
            $this->increment('sold_count', $quantity);
        } elseif ($type === 'restock' || $type === 'add') {
            $this->increment('stock', $quantity);
        }
        
        // Update needs_restock flag
        if ($this->stock <= $this->low_stock_threshold) {
            $this->needs_restock = true;
        } else {
            $this->needs_restock = false;
        }
        
        // Update status
        if ($this->stock <= 0) {
            $this->status = 'out_of_stock';
        } elseif ($this->status === 'out_of_stock' && $this->stock > 0) {
            $this->status = 'active';
        }
        
        return $this->save();
    }
    
    // Get specifications as array
    public function getSpecificationsArray()
    {
        if (empty($this->specifications)) {
            return [];
        }
        
        return is_array($this->specifications) ? $this->specifications : json_decode($this->specifications, true);
    }
    
    // Generate product URL
    public function getProductUrlAttribute()
    {
        if ($this->slug) {
            return route('products.show', $this->slug);
        }
        
        return route('products.show', $this->product_id);
    }
    
    // Helper method to get product type label
    public function getProductTypeLabelAttribute()
    {
        return $this->product_type === 'onstock' ? 'On-Stock' : 'One-Time';
    }
    
    // Helper method to get status label
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
    
    // Custom mutator for price_tiers to ensure proper JSON encoding
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
    
    // Custom accessor for price_tiers to ensure proper JSON decoding
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
}