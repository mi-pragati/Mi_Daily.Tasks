<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    protected $fillable = ['title','content','category_id'];
    public function category(){
        return $this->belongsTo(Category::class);
    }
}
