<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;




class FideliteEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
   

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Fidelite Email',
        );
    }

    /**
     * Get the message content definition.
     */
    use Queueable, SerializesModels;
    
    public $user;
    public $pdfPath;
    
    public function __construct($user, $pdfPath)
    {
        $this->user = $user;
        $this->pdfPath = $pdfPath;
    }
    public function content(): Content
    {
        return new Content(
            view: 'view.name',
        );
    }
    public function build()
    {
        return $this->view('emails.fidelite')
                    ->subject('Votre Carte de Fidélité')
                    ->attach($this->pdfPath, [
                        'as' => 'carte_fidelite.pdf',
                        'mime' => 'application/pdf',
                    ]);
    }
    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
