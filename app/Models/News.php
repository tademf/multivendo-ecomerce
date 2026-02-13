<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'image',
        'published_at',
        'expired_at'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    /**
     * Scope for active news
     */
    public function scopeActive($query)
    {
        return $query->where('published_at', '<=', now())
                     ->where(function($q) {
                         $q->where('expired_at', '>=', now())
                           ->orWhereNull('expired_at');
                     });
    }

    /**
     * Scope for latest news
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('published_at', 'desc');
    }

    /**
     * Check if news is active
     */
    public function isActive(): bool
    {
        return $this->published_at <= now() && 
               ($this->expired_at === null || $this->expired_at >= now());
    }

    /**
     * Get formatted published date
     */
    public function getFormattedPublishedDateAttribute()
    {
        return $this->published_at?->format('F j, Y');
    }

    /**
     * Get excerpt from content
     */
    public function getExcerptAttribute($length = 150)
    {
        return strlen($this->content) > $length 
               ? substr($this->content, 0, $length) . '...' 
               : $this->content;
    }

    /**
     * Get image url
     */
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return asset('images/default-news.jpg');
        }
        
        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }
        
        return asset('storage/' . $this->image);
    }
}