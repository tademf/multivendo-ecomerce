<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockHistory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'stock_before',
        'stock_after',
        'notes',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'stock_before' => 'integer',
        'stock_after' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Stock history types constants.
     */
    public const TYPE_SALE = 'sale';
    public const TYPE_RESTOCK = 'restock';
    public const TYPE_ADJUSTMENT = 'adjustment';
    public const TYPE_DAMAGE = 'damage';

    /**
     * Get the stock type options.
     *
     * @return array<string, string>
     */
    public static function getTypeOptions(): array
    {
        return [
            self::TYPE_SALE => 'Sale',
            self::TYPE_RESTOCK => 'Restock',
            self::TYPE_ADJUSTMENT => 'Adjustment',
            self::TYPE_DAMAGE => 'Damage/Loss',
        ];
    }

    /**
     * Get the human-readable type name.
     *
     * @return string
     */
    public function getTypeNameAttribute(): string
    {
        return self::getTypeOptions()[$this->type] ?? $this->type;
    }

    /**
     * Scope queries by type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope queries by product.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $productId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Scope queries by date range.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $startDate
     * @param string $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBetweenDates($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Get the quantity with sign (+/-) based on type.
     *
     * @return string
     */
    public function getQuantityWithSignAttribute(): string
    {
        if (in_array($this->type, [self::TYPE_SALE, self::TYPE_DAMAGE])) {
            return '-' . $this->quantity;
        }
        
        if (in_array($this->type, [self::TYPE_RESTOCK, self::TYPE_ADJUSTMENT])) {
            return '+' . $this->quantity;
        }
        
        return (string) $this->quantity;
    }

    /**
     * Check if this history entry increased stock.
     *
     * @return bool
     */
    public function isIncrease(): bool
    {
        return in_array($this->type, [self::TYPE_RESTOCK, self::TYPE_ADJUSTMENT]);
    }

    /**
     * Check if this history entry decreased stock.
     *
     * @return bool
     */
    public function isDecrease(): bool
    {
        return in_array($this->type, [self::TYPE_SALE, self::TYPE_DAMAGE]);
    }

    /**
     * Get the net change in stock.
     *
     * @return int
     */
    public function getNetChangeAttribute(): int
    {
        return $this->stock_after - $this->stock_before;
    }

    /**
     * Get the product associated with the stock history.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Create a stock history entry.
     *
     * @param array $data
     * @return StockHistory
     */
    public static function log(array $data): StockHistory
    {
        return self::create($data);
    }

    /**
     * Log a sale.
     *
     * @param Product $product
     * @param int $quantity
     * @param int|null $userId
     * @param string|null $notes
     * @return StockHistory
     */
    public static function logSale(Product $product, int $quantity, ?int $userId = null, ?string $notes = null): StockHistory
    {
        $stockBefore = $product->stock;
        $stockAfter = $stockBefore - $quantity;
        
        return self::create([
            'product_id' => $product->product_id ?? $product->id,
            'type' => self::TYPE_SALE,
            'quantity' => $quantity,
            'stock_before' => $stockBefore,
            'stock_after' => $stockAfter,
            'user_id' => $userId,
            'notes' => $notes,
        ]);
    }

    /**
     * Log a restock.
     *
     * @param Product $product
     * @param int $quantity
     * @param int|null $userId
     * @param string|null $notes
     * @return StockHistory
     */
    public static function logRestock(Product $product, int $quantity, ?int $userId = null, ?string $notes = null): StockHistory
    {
        $stockBefore = $product->stock;
        $stockAfter = $stockBefore + $quantity;
        
        return self::create([
            'product_id' => $product->product_id ?? $product->id,
            'type' => self::TYPE_RESTOCK,
            'quantity' => $quantity,
            'stock_before' => $stockBefore,
            'stock_after' => $stockAfter,
            'user_id' => $userId,
            'notes' => $notes,
        ]);
    }

    /**
     * Get the latest stock history for a product.
     *
     * @param int $productId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getLatestForProduct(int $productId, int $limit = 10)
    {
        return self::forProduct($productId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get stock summary for a product.
     *
     * @param int $productId
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public static function getProductSummary(int $productId, ?string $startDate = null, ?string $endDate = null): array
    {
        $query = self::forProduct($productId);
        
        if ($startDate && $endDate) {
            $query->betweenDates($startDate, $endDate);
        }
        
        $histories = $query->get();
        
        return [
            'total_sales' => $histories->where('type', self::TYPE_SALE)->sum('quantity'),
            'total_restocks' => $histories->where('type', self::TYPE_RESTOCK)->sum('quantity'),
            'total_adjustments' => $histories->where('type', self::TYPE_ADJUSTMENT)->sum('quantity'),
            'total_damages' => $histories->where('type', self::TYPE_DAMAGE)->sum('quantity'),
            'total_entries' => $histories->count(),
            'net_change' => $histories->sum('quantity'),
        ];
    }
}