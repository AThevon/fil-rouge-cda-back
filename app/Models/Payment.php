<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\PaymentStatus;

class Payment extends Model
{
   use HasFactory;

   protected $fillable = [
      'order_id',
      'stripe_session_id',
      'stripe_payment_intent_id',
      'stripe_charge_id',
      'amount',
      'currency',
      'status',
      'payment_method',
      'receipt_url',
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