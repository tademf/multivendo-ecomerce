<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdditionalImage extends Model
{
    use HasFactory;

    protected $primaryKey = 'additional_image_id';
    
    protected $fillable = [
        'product_id',
        'image_path',
        'display_order',
        'is_selected'
    ];
    
    protected $casts = [
        'display_order' => 'integer',
        'is_selected' => 'boolean'
    ];
    
    protected $appends = [
        'image_url'
    ];
    
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
    
    public function getImageUrlAttribute()
    {
        if (!$this->image_path) {
            return asset('images/default-product.png');
        }
        
        // Check if it's a URL
        if (strpos($this->image_path, 'http://') === 0 || strpos($this->image_path, 'https://') === 0) {
            return $this->image_path;
        }
        
        // Check if it's a storage path
        if (strpos($this->image_path, 'storage/') === 0) {
            return asset($this->image_path);
        }
        
        // Default to storage path
        return asset('storage/' . $this->image_path);
    }
    
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('created_at');
    }
    
    public function scopeSelected($query)
    {
        return $query->where('is_selected', true);
    }
}