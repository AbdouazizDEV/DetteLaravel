<?php

namespace App\Jobs;

use App\Services\Contracts\DatabaseServiceInterface;
use Illuminate\Support\Facades\DB;

class ArchiverDettesJob 
{
    protected $dette;

    public function __construct($dette)
    {
        $this->dette = $dette;
    }

    public function handle(DatabaseServiceInterface $databaseService)
    {
        // Prepare the data to be archived
        $archiveData = [
       
    '_id' => (string) $this->dette->id,
            'client' => [
                'surnom' => $this->dette->client_surnom, // Now available
                'telephone' => $this->dette->client_telephone, // Now available
            ],
            'details' => [
                'montant' => (string) $this->dette->montant_dette,
                'statut' => 'réglé',
            ],
            
        
        'created_at' => now()->toDateTimeString(),
        'updated_at' => now()->toDateTimeString(),
        ];
    
            //dd($archiveData);

        // Save to MongoDB
        $databaseService->saveDocument('dettes_archive', $this->dette->id, $archiveData);
    }

}
