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

    public function sendFideliteEmail($user)
    {
        $pdfPath = $this->pdfService->generateUserPdf($user);

        Mail::to($user->email)->send(new FideliteEmail($user, $pdfPath));
    }
}
