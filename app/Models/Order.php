<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'email', 'phone', 'address', 'subtotal', 'total','discount', 'final_total',
    'coupon_code', 'status','delivery_status', 'payment_method', 'payment_id','payment_details','is_reordered','return_status',
    ];

    const STATUS_PENDING   = 'Pending';
    const STATUS_COMPLETED = 'Completed';

    public function orderItems()
{
    return $this->hasMany(OrderItem::class);
}

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function returns()
{
    return $this->hasMany(OrderReturn::class);
}

public function latestReturn()
{
    return $this->hasOne(OrderReturn::class)->latestOfMany();
}

public function payment()
{
    return $this->hasOne(Payment::class);
}
protected $casts = [
    'subtotal' => 'decimal:2',
    'total' => 'decimal:2',
    'discount' => 'decimal:2',
    'final_total' => 'decimal:2',
];


}
