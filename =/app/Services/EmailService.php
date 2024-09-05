<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use App\Mail\FideliteEmail;


class EmailService
{
    protected $pdfService;

    public function __construct(PdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    public function sendFideliteEmail($client)
    {
        $pdfService = app(PdfService::class);
        $pdfPath = $pdfService->generateUserPdf($client->user);

        Mail::to($client->user->email)->send(new FideliteEmail($client, $pdfPath));
    }
}
