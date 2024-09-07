<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\FindLocalImagesJob;

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
        // Exécuter le job qui trouve toutes les images locales et déclenche leur upload
        FindLocalImagesJob::dispatch();

        $this->info(' le Job pour trouver des images locales et envoyer des téléchargements a été envoyé.\n! Bon travail !');
        return 0;
    }
}
