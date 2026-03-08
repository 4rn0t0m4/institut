<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{    use HasFactory;
    protected $fillable = [
        'user_id','number','status','subtotal','discount_total','shipping_total',
        'tax_total','total','currency','payment_method','stripe_payment_intent_id',
        'stripe_session_id','paid_at',
        'billing_first_name','billing_last_name','billing_email','billing_phone',
        'billing_address_1','billing_address_2','billing_city','billing_postcode','billing_country',
        'shipping_first_name','shipping_last_name','shipping_address_1','shipping_address_2',
        'shipping_city','shipping_postcode','shipping_country',
        'shipping_method','tracking_number','tracking_carrier','shipped_at','review_requested_at','customer_note',
        'gift_wrap','gift_type','gift_message',
    ];
    protected $casts = [
        'paid_at'=>'datetime','shipped_at'=>'datetime','review_requested_at'=>'datetime','gift_wrap'=>'boolean',
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

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
