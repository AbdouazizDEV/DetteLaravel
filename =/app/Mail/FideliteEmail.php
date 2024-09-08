<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FideliteEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $client;
    public $qrCodePath;
    public $pdfPath;

    public function __construct($client, $pdfPath)
    {
        $this->client = $client;
        $this->pdfPath = $pdfPath;
        $this->qrCodePath = storage_path('app/public/qr_codes/' . $client->user->id . '.png');
    }

    public function build()
    {
        return $this->view('emails.fidelite')
                    ->to($this->client->user->login)
                    ->subject('Carte de Fidélité')
                    ->attach($this->pdfPath)
                    ->with([
                        'client' => $this->client,
                        'qrCodePath' => $this->qrCodePath,
                    ]);
    }
}
