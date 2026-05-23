<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'category_id', 'title', 'slug', 'excerpt',
        'content', 'image', 'location', 'views_count', 'status'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function approvedComments(): HasMany
    {
        return $this->hasMany(Comment::class)->where('is_approved', true);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    public function getAverageRatingAttribute(): float
    {
        return round($this->ratings()->avg('score') ?? 0, 1);
    }

    public function getRatingCountAttribute(): int
    {
        return $this->ratings()->count();
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            if (Str::startsWith($this->image, ['http://', 'https://'])) {
                return $this->image;
            }
            return asset('storage/' . $this->image);
        }
        return 'https://placehold.co/800x400/0D8ABC/white?text=' . urlencode(Str::limit($this->title, 20));
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopePopular($query)
    {
        return $query->orderBy('views_count', 'desc');
    }

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        // Chỉ tạo slug mới khi tạo bài viết (chưa có slug), không ghi đè khi sửa
        if (empty($this->attributes['slug'])) {
            $baseSlug = Str::slug($value);
            $slug = $baseSlug . '-' . Str::random(5);
            $this->attributes['slug'] = $slug;
        }
    }

    /**
     * Sanitize nội dung HTML để tránh XSS — loại bỏ script tags nguy hiểm
     */
    public function setContentAttribute($value)
    {
        // Loại bỏ script, iframe, on* event handlers để tránh XSS
        $value = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $value);
        $value = preg_replace('/<iframe\b[^>]*>(.*?)<\/iframe>/is', '', $value);
        $value = preg_replace('/\bon\w+\s*=\s*["\'][^"\']*["\']/i', '', $value);
        $this->attributes['content'] = $value;
    }

    public function isFavoritedBy(?User $user): bool
    {
        if (!$user) return false;
        return $this->favorites()->where('user_id', $user->id)->exists();
    }
}
