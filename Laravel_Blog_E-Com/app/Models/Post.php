<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;
        protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'body',
        'category_id',
        'user_id',
        'published_at',
        'cover_url',
        'author_id',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected static function booted()
    {
        static::creating(function ($post) {
            if (empty($post->slug)) {
                $slug = Str::slug($post->title);
                $post->slug = $slug . '-' . time();
            }
        });
    }
        public function user()
        {
            return $this->belongsTo(User::class);
        }


    public function author()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
