<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'shipment_id',
        'product_id',
        'product_name',
        'quantity',
        'unit_price',
        'total_price',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'quantity' => 'integer',
    ];

    /**
     * Get the shipment that owns the item.
     */
    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    /**
     * Get the product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calculate total price.
     */
    public static function calculateTotalPrice($quantity, $unitPrice): float
    {
        return $quantity * $unitPrice;
    }

    /**
     * Get formatted unit price.
     */
    public function getFormattedUnitPriceAttribute(): string
    {
        return '₦' . number_format($this->unit_price, 2);
    }

    /**
     * Get formatted total price.
     */
    public function getFormattedTotalPriceAttribute(): string
    {
        return '₦' . number_format($this->total_price, 2);
    }
}