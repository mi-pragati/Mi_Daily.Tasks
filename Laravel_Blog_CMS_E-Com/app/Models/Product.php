<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

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
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
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
            default      => $q->orderBy('created_at', 'desc'), // newest
        };
    }
}
