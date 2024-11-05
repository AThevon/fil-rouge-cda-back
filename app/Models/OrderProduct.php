<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
    ];

    /**
     * Relation avec la commande (Order)
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relation avec le produit (Product)
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}