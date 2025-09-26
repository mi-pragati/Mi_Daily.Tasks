<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;


class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_category_id',
        'title',
        'slug',
        'description',
        'price',
        'stock',
        'status',
        'image',
        'media',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'media' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }
     public function getImageUrlAttribute(): string
    {
        if (!empty($this->image)) {
            if (Str::startsWith($this->image, ['http://', 'https://'])) {
                return $this->image;
            }
            return asset('storage/'.$this->image);
        }

       if (!empty($this->media)) {
        $media = $this->media;
        if (is_string($media)) {
            $decoded = json_decode($media, true);
            $media = (json_last_error() === JSON_ERROR_NONE) ? $decoded : [$media];
        }
        $first = is_array($media) ? reset($media) : null;
        if (!empty($first)) {
            if (Str::startsWith($first, ['http://','https://'])) {
                return $first;
            }
            return asset('storage/'.$first);
        }
    }

    // 3) default
    return asset('storage/products/default-image.jpg');
}

    public function seller()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeInCategory(Builder $q, ?string $slug): Builder
    {
        if (!$slug) return $q;
        return $q->whereHas('category', fn ($c) => $c->where('slug', $slug));
    }

    public function scopeSort(Builder $q, ?string $sort): Builder
    {
        return match ($sort) {
            'price_asc'  => $q->orderBy('price', 'asc'),
            'price_desc' => $q->orderBy('price', 'desc'),
            'name'       => $q->orderBy('title', 'asc'),
            default      => $q->orderBy('created_at', 'desc'),
        };
    }
public function getRouteKeyName()
{
    return 'slug';
}

public function reviews()
{
    return $this->hasMany(Review::class);
}

public function averageRating()
{
    return round($this->reviews()->avg('rating') ?? 0, 1);
}



}
