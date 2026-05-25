<?php

namespace App\Mail;

use App\Models\CreditNote;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CreditNoteMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public CreditNote $creditNote) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Avoir {$this->creditNote->number} — Commande #{$this->creditNote->order->number}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.credit-note',
        );
    }
}
