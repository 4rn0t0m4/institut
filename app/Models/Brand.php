<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'content', 'meta_title', 'meta_description', 'image_path', 'color'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
