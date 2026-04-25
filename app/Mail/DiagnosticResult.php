<?php

namespace App\Mail;

use App\Models\QuizCompletion;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DiagnosticResult extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public QuizCompletion $completion) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Ton diagnostic de peau — Résultat personnalisé',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.quiz.result',
        );
    }
}
