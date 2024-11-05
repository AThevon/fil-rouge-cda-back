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
        'category_id',
    ];

    protected $hidden = [
        'category_id',
    ];

    // protected $appends = ['vote_count'];

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

    /**
     * Relation avec les votes (ProductVote)
     */
    public function productVotes()
    {
        return $this->hasMany(ProductVote::class);
    }

    /**
     * Accesseur pour compter les votes dynamiquement
     */
    public function getVoteCountAttribute()
    {
        return $this->productVotes()->count();
    }
}