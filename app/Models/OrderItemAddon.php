<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class OrderItemAddon extends Model
{
    public $timestamps = false;
    protected $fillable = ['order_item_id','addon_label','addon_value','addon_price','addon_type','file_path'];
    protected $casts = ['addon_price'=>'decimal:2'];

    public function orderItem() { return $this->belongsTo(OrderItem::class); }
}
