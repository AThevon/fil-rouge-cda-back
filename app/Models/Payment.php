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
      'stripe_session_id', // ID de la session Stripe Checkout
      'stripe_payment_intent_id', // ID de l'intent de paiement
      'stripe_charge_id', // ID de la charge Stripe
      'amount',
      'currency',
      'status',
      'payment_method', // Carte, wallet, etc.
      'receipt_url', // Lien vers le reçu Stripe
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