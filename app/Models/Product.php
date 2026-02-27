<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id','name','slug','short_description','description',
        'price','sale_price','sku','stock_quantity','manage_stock','stock_status',
        'weight','dimensions','is_virtual','is_downloadable','is_featured','is_active',
        'meta_title','meta_description','featured_image_id','gallery_image_ids',
    ];
    protected $casts = [
        'dimensions'=>'array','gallery_image_ids'=>'array',
        'manage_stock'=>'boolean','is_virtual'=>'boolean',
        'is_downloadable'=>'boolean','is_featured'=>'boolean','is_active'=>'boolean',
        'price'=>'decimal:2','sale_price'=>'decimal:2',
    ];

    public function category() { return $this->belongsTo(ProductCategory::class,'category_id'); }
    public function featuredImage() { return $this->belongsTo(Media::class,'featured_image_id'); }
    public function addons() { return $this->morphMany(ProductAddon::class,'assignable'); }

    public function currentPrice(): float
    {
        return $this->sale_price ?? $this->price;
    }
}
