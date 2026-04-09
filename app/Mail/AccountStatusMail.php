<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $userName;
    public bool $isActive;
    public string $changedByName;

    public function __construct(string $userName, bool $isActive, string $changedByName)
    {
        $this->userName = $userName;
        $this->isActive = $isActive;
        $this->changedByName = $changedByName;
    }

    public function envelope(): Envelope
    {
        $subject = $this->isActive 
            ? 'Votre compte Terminus-Boutique a été activé' 
            : 'Votre compte Terminus-Boutique a été désactivé';
            
        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.account-status',
        );
    }
}
