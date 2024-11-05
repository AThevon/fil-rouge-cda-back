<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\PaymentStatus;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'amount',
        'currency',
        'status',
        'order_id',
    ];

    protected $casts = [
        'status' => PaymentStatus::class,
    ];

    /**
     * Relation avec la commande (Order)
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}