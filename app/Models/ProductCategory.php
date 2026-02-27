<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $fillable = ['parent_id','name','slug','description','image','sort_order'];

    public function parent() { return $this->belongsTo(ProductCategory::class,'parent_id'); }
    public function children() { return $this->hasMany(ProductCategory::class,'parent_id')->orderBy('sort_order'); }
    public function products() { return $this->hasMany(Product::class,'category_id'); }
}
