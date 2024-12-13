<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    /**
     * Crée une nouvelle instance du message.
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Obtient l'enveloppe du message.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bienvenue sur notre plateforme'
        );
    }

    /**
     * Obtient la définition du contenu du message.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome_user',
            with: ['user' => $this->user]
        );
    }

    /**
     * Obtient les pièces jointes du message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}