<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    // ONLY include fields that exist in your database
    protected $fillable = [
        'user_id',
        'product_id',
        'product_name',
        'product_image',
        'name',
        'payment_image',
        'amount',
        'quantity',
        'shipment_address',
        'status',
        'order_reference',
        // REMOVE these (they don't exist in your database):
        // 'email',
        // 'phone',
        // 'payment_image_original_name',
        // 'payment_method',
        // 'bank_name',
        // 'transaction_id',
        // 'verification_status',
        // 'rejection_reason',
        // 'verified_at',
        // 'verified_by',
        // 'shipping_status',
        // 'tracking_number',
        // 'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'quantity' => 'integer',
    ];

    /**
     * Get the user who made the payment
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    /**
     * Generate order reference
     */
    public static function generateOrderReference()
    {
        return 'ORD-' . date('YmdHis') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));
    }

    /**
     * Get payment image URL
     */
    public function getPaymentImageUrlAttribute()
    {
        if (!$this->payment_image) {
            return null;
        }
        
        if (str_starts_with($this->payment_image, 'http')) {
            return $this->payment_image;
        }
        
        return asset('storage/' . $this->payment_image);
    }

    /**
     * Get product image URL
     */
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

    /**
     * Check if payment is pending
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if payment is completed
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Get formatted amount in BIRR
     */
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2) . ' Birr'; // Changed ₹ to Birr
    }

    /**
     * Get status badge
     */
    public function getStatusBadgeAttribute()
    {
        $statuses = [
            'pending' => ['bg-yellow-100 text-yellow-800', 'Pending'],
            'verified' => ['bg-blue-100 text-blue-800', 'Verified'],
            'completed' => ['bg-green-100 text-green-800', 'Completed'],
            'rejected' => ['bg-red-100 text-red-800', 'Rejected'],
            'cancelled' => ['bg-gray-100 text-gray-800', 'Cancelled'],
        ];

        $status = $this->status;
        $badgeClass = $statuses[$status][0] ?? 'bg-gray-100 text-gray-800';
        $badgeText = $statuses[$status][1] ?? ucfirst($status);
        
        return [
            'text' => $badgeText,
            'class' => $badgeClass
        ];
    }
}