<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Endroid\QrCode\Encoding\Encoding;
use Exception;
class PdfService
{
    public function generateUserPdf($user)
    {
        if ($user === null) {
            throw new Exception("L'utilisateur est introuvable.");
        }
        // Générer le code QR avec toutes les données de l'utilisateur
        $qrCodeData = "Nom: {$user->nom}, prenom: {$user->prenom}, Email: {$user->login}, role: {$user->role}";
        $qrCode = Builder::create()
            ->data($qrCodeData)
            ->writer(new PngWriter())
            ->build();


        $qrCodePath = storage_path('app/public/qr_codes/' . $user->id . '.png');
        Storage::put('public/qr_codes/' . $user->id . '.png', $qrCode->getString());

        // Charger la vue PDF stylisée
        $pdf = Pdf::loadView('pdf.carte_fidelite', ['user' => $user, 'qrCodePath' => $qrCodePath]);

        // Enregistrer le PDF sur le disque
        $pdfDirectory = storage_path('app/public/cartes_fidelite/');

        // Vérifier si le répertoire existe, sinon le créer
        if (!File::exists($pdfDirectory)) {
            File::makeDirectory($pdfDirectory, 0755, true);
        }

        $pdfPath = $pdfDirectory . $user->id . '.pdf';
        $pdf->save($pdfPath);

        return $pdfPath;
    }
}
