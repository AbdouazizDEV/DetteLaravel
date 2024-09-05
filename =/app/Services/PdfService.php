<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;



use Endroid\QrCode\Encoding\Encoding;
class PdfService
{
    public function generateUserPdf($user)
    {
        // Générer le QR code
        $qrCode = Builder::create()
            ->writer(new PngWriter())
            ->data('Données que vous voulez encoder') // Vous pouvez encoder par exemple $user->id ou $user->email
            ->encoding(new Encoding('UTF-8'))
            // Utiliser la constante de niveau correct
            ->build();
    
        // Chemin pour enregistrer le QR code
        $qrCodePath = storage_path('app/public/qr_codes/' . $user->id . '.png');
        
        // Créer le répertoire des QR codes s'il n'existe pas
        $qrCodeDirectory = storage_path('app/public/qr_codes/');
        if (!File::exists($qrCodeDirectory)) {
            File::makeDirectory($qrCodeDirectory, 0755, true);
        }
    
        // Sauvegarder l'image du QR code
        file_put_contents($qrCodePath, $qrCode->getString());
    
        // Chemin pour les cartes de fidélité
        $pdfDirectory = storage_path('storage/app/public/cartes_fidelites/');
        if (!File::exists($pdfDirectory)) {
            File::makeDirectory($pdfDirectory, 0755, true);
        }
    
        // Créer le PDF
        //$pdf = Pdf::loadView('pdf.carte_fidelite', ['user' => $user, 'qrCodePath' => $qrCodePath]);
        $pdf = Pdf::loadView('pdf.carte_fidelite', ['user' => $user, 'qrCodePath' => $qrCodePath]);// Enregistrer le PDF sur le disque
        $pdfPath = $pdfDirectory . $user->id . '.pdf';
        $pdf->save($pdfPath);
    
        return $pdfPath;
    }
}
