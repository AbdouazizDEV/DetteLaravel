<?php
// app/Jobs/UploadUserPhotoJob.php

namespace App\Jobs;

use App\Models\User;
use Cloudinary\Cloudinary;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class UploadUserPhotoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $imagePath;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, $imagePath)
    {
        $this->user = $user;
        $this->imagePath = $imagePath;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Initialiser Cloudinary avec vos paramètres d'environnement
            $cloudinary = new Cloudinary();

            // Téléchargement de l'image vers Cloudinary
            $upload = $cloudinary->uploadApi()->upload($this->imagePath, [
                'folder' => 'avatars',
                'public_id' => 'user_' . $this->user->id,
                'overwrite' => true,
                'resource_type' => 'image',
            ]);

            // Mise à jour du chemin de la photo dans la base de données
            $this->user->photo = $upload['secure_url'];
            $this->user->save();

            // Supprimer le fichier temporaire
            Storage::delete($this->imagePath);
        } catch (\Exception $e) {
            // Si l'upload échoue, logguer l'erreur et laisser l'image en base64
            \Log::error('Upload failed: ' . $e->getMessage());
        }
    }
}
