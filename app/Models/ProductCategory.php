<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $fillable = ['parent_id','name','slug','description','image','sort_order'];

    public function parent() { return $this->belongsTo(ProductCategory::class,'parent_id'); }
    public function children() { return $this->hasMany(ProductCategory::class,'parent_id')->orderBy('sort_order'); }
    public function products() { return $this->hasMany(Product::class,'category_id'); }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * IDs de cette catégorie + enfants + parent (filtrage bidirectionnel).
     */
    public function familyIds(): \Illuminate\Support\Collection
    {
        $ids = collect([$this->id]);
        $ids = $ids->merge($this->children->pluck('id'));

        if ($this->parent_id) {
            $ids->push($this->parent_id);
        }

        return $ids;
    }
}
