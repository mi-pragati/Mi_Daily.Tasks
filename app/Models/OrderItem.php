<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use App\Models\Product;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'title',
        'price',
        'qty',
        'subtotal',
        'image',
    ];

    /**
     * The order this item belongs to.
     */
    public function order()
    {
    return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    /**
     * The product associated with this item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
