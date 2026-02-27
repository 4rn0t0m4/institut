<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProductAddon extends Model
{
    protected $fillable = [
        'group_id','name','label','type','price','price_type',
        'required','options','settings','sort_order',
    ];
    protected $casts = [
        'options'=>'array','settings'=>'array',
        'required'=>'boolean','price'=>'decimal:2',
    ];

    public function group() { return $this->belongsTo(ProductAddonFieldGroup::class,'group_id'); }
    public function assignable() { return $this->morphTo(); }
}
