<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'product_id',
        'product_name',
        'product_image',
        'name',
        'email',
        'phone',
        'shipment_address',
        'payment_image',
        'payment_image_original_name',
        'amount',
        'quantity',
        'payment_method',
        'bank_name',
        'transaction_id',
        'order_reference',
        'status',
        'verification_status',
        'rejection_reason',
        'verified_at',
        'verified_by',
        'shipping_status',
        'tracking_number',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'quantity' => 'integer',
        'verified_at' => 'datetime',
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
     * Get the admin who verified
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Generate order reference
     */
    public static function generateOrderReference()
    {
        return 'ORD-' . date('YmdHis') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));
    }

    /**
     * Generate transaction ID
     */
    public static function generateTransactionId()
    {
        return 'TXN-' . date('YmdHis') . '-' . strtoupper(substr(uniqid(), -8));
    }

    /**
     * Get payment image URL - FIXED
     */
    public function getPaymentImageUrlAttribute()
    {
        if (!$this->payment_image) {
            return null;
        }
        
        // FIX: Check if string starts with http/https
        if (str_starts_with($this->payment_image, 'http://') || str_starts_with($this->payment_image, 'https://')) {
            return $this->payment_image;
        }
        
        return asset('storage/' . $this->payment_image);
    }

    /**
     * Get product image URL - FIXED
     */
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

    /**
     * Check if payment is pending
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if payment is verified
     */
    public function isVerified()
    {
        return $this->verification_status === 'verified';
    }

    /**
     * Check if payment is completed
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute()
    {
        return '₹' . number_format($this->amount, 2);
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