<?php

namespace App\Services;

use Cloudinary\Api\Upload\UploadApi;

class CloudinaryService
{
    public function uploadToCloudinary($filePath)
    {
        try {
            $result = (new UploadApi())->upload($filePath);

            // Vérification de la réponse pour confirmer que l'image est bien uploadée
            if (isset($result['secure_url'])) {
                // L'image a été uploadée avec succès
                return $result['secure_url']; // Retourne l'URL sécurisée de l'image
            } else {
                // Si l'URL n'est pas présente, l'upload a échoué ou est incomplet
                throw new \Exception('Failed to upload image to Cloudinary.');
            }
        } catch (\Exception $e) {
            // Gestion de l'erreur en cas d'échec de l'upload
            return null;
        }
    }
}
