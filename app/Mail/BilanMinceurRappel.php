<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BilanMinceurRappel extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $data) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Demande de rappel — Bilan minceur de {$this->data['prenom']} {$this->data['nom']}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.bilan-minceur-rappel',
        );
    }
}
