<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'delivery_address',
        'status',
        'cancellation_reason',
        'tracking_number',
        'carrier',
        'shipping_cost',
        'total_amount',
        'notes',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'shipping_cost' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Status constants.
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * Get status options.
     *
     * @return array<string, string>
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_SHIPPED => 'Shipped',
            self::STATUS_DELIVERED => 'Delivered',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    /**
     * Get human-readable status.
     */
    public function getStatusTextAttribute(): string
    {
        return self::getStatusOptions()[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Check if shipment is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if shipment is processing.
     */
    public function isProcessing(): bool
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    /**
     * Check if shipment is shipped.
     */
    public function isShipped(): bool
    {
        return $this->status === self::STATUS_SHIPPED;
    }

    /**
     * Check if shipment is delivered.
     */
    public function isDelivered(): bool
    {
        return $this->status === self::STATUS_DELIVERED;
    }

    /**
     * Check if shipment is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Cancel the shipment with a reason.
     */
    public function cancel(string $reason): bool
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'cancellation_reason' => $reason,
            'cancelled_at' => now(),
        ]);

        return true;
    }

    /**
     * Mark as shipped.
     */
    public function markAsShipped(?string $trackingNumber = null, ?string $carrier = null): bool
    {
        $this->update([
            'status' => self::STATUS_SHIPPED,
            'tracking_number' => $trackingNumber,
            'carrier' => $carrier,
            'shipped_at' => now(),
        ]);

        return true;
    }

    /**
     * Mark as delivered.
     */
    public function markAsDelivered(): bool
    {
        $this->update([
            'status' => self::STATUS_DELIVERED,
            'delivered_at' => now(),
        ]);

        return true;
    }

    /**
     * Calculate total amount (items + shipping).
     */
    public function calculateTotal(): float
    {
        $itemsTotal = $this->items()->sum('total_price');
        return $itemsTotal + $this->shipping_cost;
    }

    /**
     * Get the user that owns the shipment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items for the shipment.
     */
    public function items(): HasMany
    {
        return $this->hasMany(ShipmentItem::class);
    }

    /**
     * Scope to get shipments by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get shipments for a user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get pending shipments.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope to get shipped shipments.
     */
    public function scopeShipped($query)
    {
        return $query->where('status', self::STATUS_SHIPPED);
    }

    /**
     * Scope to get delivered shipments.
     */
    public function scopeDelivered($query)
    {
        return $query->where('status', self::STATUS_DELIVERED);
    }

    /**
     * Scope to get cancelled shipments.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    /**
     * Get formatted total amount.
     */
    public function getFormattedTotalAttribute(): string
    {
        return '₦' . number_format($this->total_amount, 2);
    }

    /**
     * Get formatted shipping cost.
     */
    public function getFormattedShippingCostAttribute(): string
    {
        return '₦' . number_format($this->shipping_cost, 2);
    }
}