<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditNote extends Model
{
    protected $fillable = [
        'order_id',
        'number',
        'amount',
        'reason',
        'stripe_refunded',
        'stripe_refund_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'stripe_refunded' => 'boolean',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    protected static function booted(): void
    {
        static::creating(function (CreditNote $note) {
            if (! $note->number) {
                $note->number = 'AV-'.strtoupper(uniqid());
            }
        });
    }
}
