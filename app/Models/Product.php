<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::saved(fn () => cache()->forget('header_navigation'));
        static::deleted(fn () => cache()->forget('header_navigation'));
    }

    protected $fillable = [
        'category_id', 'brand_id', 'name', 'slug', 'short_description', 'description',
        'team_recommendation', 'benefits', 'usage_instructions', 'composition',
        'price', 'sale_price', 'sku', 'stock_quantity', 'manage_stock', 'stock_status',
        'weight', 'unit_measure', 'dimensions', 'is_virtual', 'is_downloadable', 'is_featured', 'is_active',
        'personalizable', 'personalization_price',
        'meta_title', 'meta_description', 'featured_image_id', 'gallery_image_ids',
    ];

    protected $casts = [
        'dimensions' => 'array', 'gallery_image_ids' => 'array',
        'manage_stock' => 'boolean', 'is_virtual' => 'boolean',
        'is_downloadable' => 'boolean', 'is_featured' => 'boolean', 'is_active' => 'boolean',
        'personalizable' => 'boolean', 'personalization_price' => 'decimal:2',
        'price' => 'decimal:2', 'sale_price' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function featuredImage()
    {
        return $this->belongsTo(Media::class, 'featured_image_id');
    }

    public function addonAssignments()
    {
        return $this->morphMany(ProductAddonAssignment::class, 'assignable');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function tags()
    {
        return $this->belongsToMany(ProductTag::class);
    }

    public function stockNotifications()
    {
        return $this->hasMany(StockNotification::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(ProductReview::class)->approved();
    }

    public function galleryImages(): \Illuminate\Database\Eloquent\Collection
    {
        $ids = $this->gallery_image_ids ?? [];
        if (empty($ids)) {
            return new \Illuminate\Database\Eloquent\Collection;
        }

        return Media::whereIn('id', $ids)->orderByRaw('FIELD(id,'.implode(',', $ids).')')->get();
    }

    public function currentPrice(): float
    {
        return $this->sale_price ?? $this->price;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where(function ($q) {
            $q->where('manage_stock', false)
                ->orWhere('stock_quantity', '>', 0);
        });
    }

    public function scopeOnSale($query)
    {
        return $query->whereNotNull('sale_price');
    }

    public function scopeVisibleTo($query, ?User $user)
    {
        if (! $user?->is_admin) {
            $query->where('is_active', true);
        }

        return $query;
    }

    public function url(): string
    {
        $category = $this->relationLoaded('category') ? $this->category : $this->category()->with('parent')->first();

        if ($category && $category->parent_id) {
            $parent = $category->relationLoaded('parent') ? $category->parent : $category->parent()->first();

            return url("boutique/{$parent->slug}/{$category->slug}/{$this->slug}");
        }

        if ($category) {
            return url("boutique/{$category->slug}/{$this->slug}");
        }

        return url("boutique/{$this->slug}");
    }
}
