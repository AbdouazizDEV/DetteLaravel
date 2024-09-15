<?php

namespace App\Http\Controllers;

use App\Services\Contracts\DatabaseServiceInterface;
use App\Jobs\ArchiverDettesJob;
use Bus;
use Illuminate\Support\Facades\DB;

class ArchiveController extends Controller
{
    protected $databaseService;

    public function __construct(DatabaseServiceInterface $databaseService)
    {
        $this->databaseService = $databaseService;
    }

    public function getArchivedDettes()
    {
        // Récupération des dettes soldées
       
        $detteData = DB::table('dettes')
        ->leftJoin('paiements', 'dettes.id', '=', 'paiements.dette_id')
        ->leftJoin('clients', 'dettes.clientId', '=', 'clients.id') // Join to get client info
        ->select(
            'dettes.id', 
            DB::raw('COALESCE(SUM(paiements.montant), 0) AS total_paye'), 
            'dettes.montant AS montant_dette',
            'clients.surnom AS client_surnom',  // Select client surname
            'clients.telephone_portable AS client_telephone' // Select client phone
        )
        ->groupBy('dettes.id', 'dettes.montant', 'clients.surnom', 'clients.telephone_portable')
        ->havingRaw('COALESCE(SUM(paiements.montant), 0) >= dettes.montant')
        ->get();
    //dd($detteData); // Debug pour vérifier les résultats
        foreach ($detteData as $dette) {
            // Dispatch the job to archive each settled debt
            Bus::dispatch(new ArchiverDettesJob($dette));
             // Supprimer la dette des dettes actuelles ou marquer comme archivée
             DB::table('dettes')->where('id', $dette->id)->delete(); // ou bien update status à "archivé"
        }

        return response()->json(['message' => 'Dettes archivées avec succès!']);
    }   
}
