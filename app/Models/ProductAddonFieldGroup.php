<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAddonFieldGroup extends Model
{
    protected $fillable = ['name', 'description', 'sort_order', 'is_global'];

    protected $casts = ['is_global' => 'boolean'];

    public function addons()
    {
        return $this->hasMany(ProductAddon::class, 'group_id')->orderBy('sort_order');
    }
}
