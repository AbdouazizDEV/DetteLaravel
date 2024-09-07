<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Jobs\UploadUserImageJob;

class FindLocalImagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Récupérer tous les utilisateurs qui ont une photo stockée localement (chemin local)
        $users = User::where('photo', 'LIKE', '%/public/%')
            ->where('photo', '!=', 'default_avatar.png')
            ->get();

        // S'il n'y a aucun utilisateur avec une photo locale, arrêter le job
        if ($users->isEmpty()) {
            \Log::info('Aucun utilisateur avec des images locales trouvé.');
            return;
        }

        foreach ($users as $user) {
            \Log::info("Distribution de la tâche de téléchargement pour l'ID utilisateur : {$user->id}");

            // Exécuter le job pour uploader l'image
            UploadUserImageJob::dispatch($user, $user->photo);
        }
    }
}
