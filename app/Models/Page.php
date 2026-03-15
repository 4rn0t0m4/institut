<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id', 'title', 'slug', 'content', 'status', 'template',
        'meta_title', 'meta_description', 'featured_image', 'sort_order', 'published_at',
    ];

    protected $casts = ['published_at' => 'datetime'];

    public function parent()
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Page::class, 'parent_id')->orderBy('sort_order');
    }
}
