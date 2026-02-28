<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAddonAssignment extends Model
{
    public $timestamps = false;
    protected $fillable = ['addon_id', 'assignable_type', 'assignable_id', 'mode'];

    public function addon()
    {
        return $this->belongsTo(ProductAddon::class, 'addon_id');
    }

    public function assignable()
    {
        return $this->morphTo();
    }
}
