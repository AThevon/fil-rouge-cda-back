<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\OrderStatus;

return new class extends Migration {
   /**
    * Run the migrations.
    */
   public function up(): void
   {
      Schema::create('orders', function (Blueprint $table) {
         $table->id();
         $table->foreignId('user_id')->constrained()->onDelete('cascade');
         $table->integer('total_price')->unsigned();
         $table->enum('status', [OrderStatus::PENDING->value, OrderStatus::COMPLETED->value, OrderStatus::CANCELED->value])->default(OrderStatus::PENDING->value);
         $table->timestamps();
      });

      Schema::create('order_products', function (Blueprint $table) {
         $table->id();
         $table->foreignId('order_id')->constrained()->onDelete('cascade');
         $table->foreignId('product_id')->constrained()->onDelete('cascade');
         $table->integer('quantity');
         $table->integer('price')->unsigned();
         $table->timestamps();
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('orders');
      Schema::dropIfExists('order_products');
   }
};
