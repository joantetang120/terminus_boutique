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

    // On déclare la variable publique pour qu'elle soit accessible dans la vue Blade
    public $code;

    /**
     * On passe le code généré au constructeur
     */
    public function __construct($code)
    {
        $this->code = $code;
    }

    /**
     * Définition de l'objet du mail
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Code de vérification - Modification du profil',
        );
    }

    /**
     * Définition de la vue et passage du code
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.profile_code', // Le fichier qu'on va créer dans resources/views/emails/
            with: [
                'code' => $this->code,
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