<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
  use HasFactory;

  protected $fillable = ['url', 'type', 'imageable_type', 'imageable_id'];

  protected $hidden = [
    'id',
    'imageable_type',
    'imageable_id',
    'created_at',
    'updated_at',
    'type',
  ];

  public function imageable()
  {
    return $this->morphTo();
  }
}