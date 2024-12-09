<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
   /**
    * Run the migrations.
    */
   public function up(): void
   {
      Schema::create('payments', function (Blueprint $table) {
         $table->id();
         $table->foreignId('order_id')->constrained()->onDelete('cascade'); // Lien avec les commandes
         $table->string('stripe_session_id')->nullable();
         $table->string('stripe_payment_intent_id')->nullable();
         $table->string('stripe_charge_id')->nullable();
         $table->integer('amount')->unsigned(); // Montant en centimes
         $table->string('currency', 10)->default('eur');
         $table->string('payment_method')->nullable(); // Ex: card
         $table->string('receipt_url')->nullable(); // Lien vers le reçu Stripe
         $table->string('status')->default('pending'); // Statut du paiement
         $table->timestamps();
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('payments');
   }
};