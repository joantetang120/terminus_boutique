<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $userName;
    public string $createdByName;

    public function __construct(string $userName, string $createdByName = '')
    {
        $this->userName = $userName;
        $this->createdByName = $createdByName;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bienvenue sur Terminus-Boutique',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome',
        );
    }
}
