<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'shipments';

    protected $fillable = [
        'user_id',
        'payment_id',
        'product_id',
        'product_name',
        'product_image',
        'name',
        'email',
        'phone',
        'amount',
        'quantity',
        'shipment_address',
        'payment_image',
        'status',
        'cancellation_reason',
        'order_number',
        'vendor_id',
        'vendor_account_number',
        'payment_method',
        'payment_status',
        'payment_reference',
        'shipping_status',
        'tracking_number',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'quantity' => 'integer',
    ];

    // Relationships
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

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    // Alias for customer (for vendor view)
    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ✅ ADD THIS: Messages relationship
    public function messages()
    {
        return $this->hasMany(Message::class, 'shipment_id');
    }

    // ✅ ADD THIS: Helper to get other user in chat
    public function getOtherUser($currentUserId)
    {
        if ($currentUserId === $this->user_id) {
            // Current user is customer, return vendor
            return $this->product->user ?? $this->vendor;
        } else {
            // Current user is vendor, return customer
            return $this->user;
        }
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

    // Get status color - Vue expects this
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

    // Get product image URL
    public function getProductImageUrlAttribute()
    {
        if (!$this->product_image) {
            return $this->product?->main_image_url ?? asset('images/default-product.png');
        }
        
        if (str_starts_with($this->product_image, 'http')) {
            return $this->product_image;
        }
        
        return asset('storage/' . $this->product_image);
    }

    // Get payment image URL
    public function getPaymentImageUrlAttribute()
    {
        if (!$this->payment_image) {
            return asset('images/default-payment.png');
        }
        
        if (str_starts_with($this->payment_image, 'http')) {
            return $this->payment_image;
        }
        
        return asset('storage/' . $this->payment_image);
    }

    // For Vue compatibility - return order_number or id
    public function getOrderNumberAttribute()
    {
        return $this->attributes['order_number'] ?? '#' . $this->id;
    }

    // Get formatted amount with Birr
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2) . ' Birr';
    }

    // Get payment status text
    public function getPaymentStatusTextAttribute()
    {
        $statuses = [
            'pending' => 'Pending',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
        
        return $statuses[$this->payment_status] ?? $this->payment_status;
    }

    // Check if shipment can be cancelled
    public function getCanCancelAttribute()
    {
        return in_array($this->status, ['pending', 'processing']);
    }
}