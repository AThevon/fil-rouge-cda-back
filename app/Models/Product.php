<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'woodies',
        'category_id',
    ];

    protected $hidden = [
        'category_id',
    ];

    /**
     * Relation ManyToOne avec Category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relation OneToMany avec OrderProduct
     */
    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }

    /**
     * Relation ManyToMany avec Order via OrderProduct
     */
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_products')
            ->withPivot('price', 'quantity');
    }
}