<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payment_id',
        'product_id',
        'product_name',
        'product_image',
        'name',
        'amount',
        'quantity',
        'shipment_address',
        'payment_image',
        'status',
        'cancellation_reason',
        'order_number',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    // Status options
    public static function statusOptions()
    {
        return [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
        ];
    }

    // Get status color
    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'processing' => 'bg-blue-100 text-blue-800',
            'shipped' => 'bg-purple-100 text-purple-800',
            'delivered' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
        ];

        return $colors[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    // Get product image URL - FIXED
    public function getProductImageUrlAttribute()
    {
        if (!$this->product_image) {
            return $this->product?->main_image_url ?? asset('images/default-product.png');
        }
        
        // FIX: Check if string starts with http/https
        if (str_starts_with($this->product_image, 'http://') || str_starts_with($this->product_image, 'https://')) {
            return $this->product_image;
        }
        
        return asset('storage/' . $this->product_image);
    }

    // Get payment image URL - FIXED
    public function getPaymentImageUrlAttribute()
    {
        if (!$this->payment_image) {
            return asset('images/default-payment.png');
        }
        
        // FIX: Check if string starts with http/https
        if (str_starts_with($this->payment_image, 'http://') || str_starts_with($this->payment_image, 'https://')) {
            return $this->payment_image;
        }
        
        return asset('storage/' . $this->payment_image);
    }
}