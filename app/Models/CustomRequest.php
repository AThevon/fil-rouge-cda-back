<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Image;
use App\Models\Category;
use App\Models\User;

class CustomRequest extends Model
{
  use HasFactory;

  protected $fillable = [
    'email',
    'message',
    'category_id',
    'user_id',
  ];

  protected $hidden = [
    'user_id',
    'category_id',
    'updated_at',
  ];

  /**
   * Relation avec le modèle `User`.
   * Un CustomRequest appartient à un utilisateur.
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  /**
   * Relation avec le modèle `Category`.
   * Un CustomRequest appartient à une catégorie.
   */
  public function category()
  {
    return $this->belongsTo(Category::class);
  }

  /**
   * Relation polymorphique avec le modèle `Image`.
   * Un CustomRequest peut avoir plusieurs images.
   */
  public function images()
  {
    return $this->morphMany(Image::class, 'imageable');
  }
}