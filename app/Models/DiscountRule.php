<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountRule extends Model
{    use HasFactory;
    protected $fillable = [
        'name','coupon_code','is_active','type','discount_type','discount_amount',
        'target_categories','target_products',
        'min_cart_value','max_cart_value','min_quantity','max_quantity',
        'starts_at','ends_at','stackable','sort_order',
    ];
    protected $casts = [
        'is_active'=>'boolean','stackable'=>'boolean',
        'target_categories'=>'array','target_products'=>'array',
        'discount_amount'=>'decimal:2',
        'min_cart_value'=>'decimal:2','max_cart_value'=>'decimal:2',
        'starts_at'=>'date','ends_at'=>'date',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(fn($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()));
    }
}
