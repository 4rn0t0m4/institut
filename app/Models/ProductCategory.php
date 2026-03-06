<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{    use HasFactory;
    protected $fillable = ['parent_id','name','slug','description','image','sort_order'];

    public function parent() { return $this->belongsTo(ProductCategory::class,'parent_id'); }
    public function children() { return $this->hasMany(ProductCategory::class,'parent_id')->orderBy('sort_order'); }
    public function products() { return $this->hasMany(Product::class,'category_id'); }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * IDs pour le filtrage : catégorie courante + ses enfants (pas le parent).
     */
    public function familyIds(): \Illuminate\Support\Collection
    {
        return collect([$this->id])->merge($this->children->pluck('id'));
    }

    public function url(): string
    {
        if ($this->parent) {
            return url("boutique/{$this->parent->slug}/{$this->slug}");
        }

        return url("boutique/{$this->slug}");
    }
}
