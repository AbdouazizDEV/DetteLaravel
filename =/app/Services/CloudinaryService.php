<?php
// app/Services/CloudinaryService.php

namespace App\Services;

use Cloudinary\Api\Upload\UploadApi;

class CloudinaryService
{
    private $uploadApi;

    public function __construct(UploadApi $uploadApi)
    {
        $this->uploadApi = $uploadApi;
    }

    public function upload($file, $options)
    {
        return $this->uploadApi->upload($file, $options);
    }

    public function uploadToCloudinary($filePath)
    {
        try {
            $result = $this->uploadApi->upload($filePath, [
                'folder' => 'avatars',
                'public_id' => 'user_' . pathinfo($filePath, PATHINFO_FILENAME),
                'overwrite' => true, 
                'resource_type' => 'image',
            ]);
            dd($result);
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
            \Log::error('Upload failed: ' . $e->getMessage());
            return null;
        }
    }
}
