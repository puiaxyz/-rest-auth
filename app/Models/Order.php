<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'total_price', 'status'
    ];

    /**
     * Define the relationship between an order and its cart items.
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'user_id', 'user_id');
    }

    /**
     * Define the relationship between an order and the user who made it.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
