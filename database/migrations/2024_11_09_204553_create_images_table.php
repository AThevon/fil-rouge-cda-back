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
    Schema::create('images', function (Blueprint $table) {
      $table->id();
      $table->string('url');
      $table->string('type')->default('public'); // Type de l'image (public/private)
      $table->string('imageable_type'); // Le type du modèle associé (ex : App\Models\Product)
      $table->unsignedBigInteger('imageable_id'); // ID de l'entité associée
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('images');
  }
};
