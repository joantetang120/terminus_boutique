<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProfileUpdateVerification extends Mailable
{
    use Queueable, SerializesModels;

    public $code;
    public $changes;
    public $emailChanged;

    /**
     * Create a new message instance.
     */
    public function __construct($code, $changes = [], $emailChanged = false)
    {
        $this->code = $code;
        $this->changes = $changes;
        $this->emailChanged = $emailChanged;
    }

    /**
     * Définition de l'objet du mail
     */
    public function envelope(): Envelope
    {
        $subject = $this->emailChanged
            ? 'Code de vérification - Changement d\'adresse email'
            : 'Code de vérification - Modification du profil';

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Définition de la vue et passage des données
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.profile_code',
            with: [
                'code' => $this->code,
                'changes' => $this->changes,
                'emailChanged' => $this->emailChanged,
            ],
        );
    }

    /**
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}