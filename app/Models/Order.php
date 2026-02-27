<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id','number','status','subtotal','discount_total','shipping_total',
        'tax_total','total','currency','payment_method','stripe_payment_intent_id',
        'stripe_session_id','paid_at',
        'billing_first_name','billing_last_name','billing_email','billing_phone',
        'billing_address_1','billing_address_2','billing_city','billing_postcode','billing_country',
        'shipping_first_name','shipping_last_name','shipping_address_1','shipping_address_2',
        'shipping_city','shipping_postcode','shipping_country',
        'shipping_method','tracking_number','tracking_carrier','customer_note',
    ];
    protected $casts = [
        'paid_at'=>'datetime',
        'subtotal'=>'decimal:2','discount_total'=>'decimal:2',
        'shipping_total'=>'decimal:2','tax_total'=>'decimal:2','total'=>'decimal:2',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function items() { return $this->hasMany(OrderItem::class); }

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (! $order->number) {
                $order->number = 'CMD-' . strtoupper(uniqid());
            }
        });
    }
}
