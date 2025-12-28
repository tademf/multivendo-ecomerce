<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Discount extends Model
{
    protected $primaryKey = 'discount_id';
    
    protected $fillable = [
        'discount_name',
        'product_id',
        'discount_amount',
        'user_id',
        'start_date',
        'end_date',
        'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'discount_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'upcoming');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Helper methods
    public function isActive(): bool
    {
        $now = Carbon::now();
        return $this->status === 'active' && 
               $now->between($this->start_date, $this->end_date);
    }

    public function getRemainingDaysAttribute(): int
    {
        $end = Carbon::parse($this->end_date);
        $now = Carbon::now();
        
        if ($now->greaterThan($end)) {
            return 0;
        }
        
        return $now->diffInDays($end) + 1;
    }

    public function getProgressPercentageAttribute(): float
    {
        $start = Carbon::parse($this->start_date);
        $end = Carbon::parse($this->end_date);
        $now = Carbon::now();
        
        $totalDuration = $end->diffInDays($start);
        $elapsed = $now->diffInDays($start);
        
        if ($totalDuration <= 0) return 100;
        if ($elapsed <= 0) return 0;
        
        $percentage = ($elapsed / $totalDuration) * 100;
        return min(max($percentage, 0), 100);
    }
}