<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AbandonedCartReminder extends Mailable
{
    use Queueable, SerializesModels;

    public string $discountCode;

    public function __construct(public Order $order, string $discountCode)
    {
        $this->discountCode = $discountCode;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre commande vous attend — profitez de -10% !',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.abandoned-cart',
        );
    }
}
