<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Jobs\UploadUserImageJob;

class RetryImageUpload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'image:retry-upload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retry uploading images stored locally in the database to Cloudinary';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Récupérer tous les utilisateurs qui ont une photo stockée localement (chemin local)
        $users = User::where('photo', 'LIKE', '%/public/%')
            ->where('photo', '!=', 'default_avatar.png')
            ->get();

        // S'il n'y a aucun utilisateur avec une photo locale, afficher un message et terminer le script
        if ($users->isEmpty()) {
            $this->info('No users with local images found.');
            return 0;
        }

        foreach ($users as $user) {
            $this->info("Retrying upload for user ID: {$user->id}");

            // Obtenir le chemin local de l'image
            $localImagePath = $user->photo;

            // Vérifier si le fichier existe
            if (!file_exists($localImagePath)) {
                $this->error("Image file not found for user ID: {$user->id} at path: {$localImagePath}");
                continue;
            }

            // Exécuter le job pour uploader l'image
            UploadUserImageJob::dispatch($user, $localImagePath);
        }

        $this->info('A new attempt to upload local images to Cloudinary has been dispatched.');
        return 0;
    }
}
