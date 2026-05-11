<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewOrderAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        $clientName = trim(($this->order->billing_first_name ?? '').' '.($this->order->billing_last_name ?? '')) ?: 'Client';

        return new Envelope(
            from: new Address($this->order->billing_email, $clientName),
            replyTo: [new Address($this->order->billing_email, $clientName)],
            subject: "Nouvelle commande #{$this->order->number} — {$this->order->total} €",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.admin-notification',
        );
    }
}
