<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Http\Requests\ValidateClientPostRequest;
use App\Http\Requests\ValidateClientUpdateRequest;
use App\Http\Requests\ValidateUserPostRequest;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Support\Facades\Validator;
use App\Models\Dette;
use App\Services\Contracts\ClientServiceInterface;

use Spatie\QueryBuilder\AllowedSort;
use Illuminate\Support\Facades\Auth;


class ClientController extends Controller
{
    private $clientService;

    public function __construct(ClientServiceInterface $clientService)
    {
        $this->clientService = $clientService;
    }
     public function index(Request $request)
    {
        // Initialisation de la requête de base
        $query = QueryBuilder::for(Client::class)
            ->allowedFilters([
                AllowedFilter::partial('surnom'),    // Filtrage partiel par surnom
                AllowedFilter::partial('telephone_portable'), // Filtrage partiel par telephone_portable
                AllowedFilter::exact('user_id'),  // Filtrage exact par user_id
            ])
            ->allowedSorts([
                'surnom', 'telephone_portable', 'created_at'     // Trie par surnom, téléphone ou date de création
            ])
            ->with('user');  // Eager Loading pour la relation 'user'

        // Filtrage par compte (comptes actifs ou non)
        $compte = $request->query('compte');
        if ($compte === 'oui') {
            $query->whereHas('user');  // Filtrer les clients qui ont un compte utilisateur
        } elseif ($compte === 'non') {
            $query->whereDoesntHave('user');  // Filtrer les clients sans compte utilisateur
        }

        // Filtrage par état du compte (actif ou désactivé)
        $active = $request->query('active');
        if ($active === 'oui') {
            $query->whereHas('user', function($q) {
                $q->where('active', true);  // Filtrer les clients avec des comptes actifs
            });
        } elseif ($active === 'non') {
            $query->whereHas('user', function($q) {
                $q->where('active', false);  // Filtrer les clients avec des comptes désactivés
            });
        }

        // Pagination
        $clients = $query->paginate(5);

        // Vérification si la collection de clients est vide
        if ($clients->isEmpty()) {
            return response()->json([
                'status' => 200,
                'data' => null,
                'message' => 'Pas de clients trouvés'
            ]);
        }

        // Retourne une collection paginée sous forme de JSON
        return response()->json([
            'status' => 200,
            'data' => $clients,
            'message' => 'Liste des clients récupérée avec succès.'
        ]);
    }

    public function show($id)
    {
        
        /* return response()->json($client); */
        $client = QueryBuilder::for(Client::class)
        ->with('user')                        // Eager Loading pour la relation 'user'
        ->findOrFail($id);

        return response()->json($client);
    }

    public function searchByTelephone( $telephone)
    {
       // $telephone = $request->input('telephone');
    
        // Rechercher le client par numéro de téléphone
        $client = Client::where('telephone_portable', $telephone)->first();
    
        if (!$client) {
            return response()->json([
                'status' => 404,
                'data' => null,
                'message' => 'Client non trouvé'
            ], 404);
        }
    
        return response()->json([
            'status' => 200,
            'data' => $client,
            'message' => 'Client trouvé'
        ]);
    }
    
    public function store(ValidateClientPostRequest $request)
    {
        try {
            $client = $this->clientService->storeClient($request->validated());
            return response()->json([
                'status' => 200,
                'data' => $client,
                'message' => 'Client ajouté avec succès.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }
  
    public function listDettes($id)
    {
        $client = Client::find($id);
    
        if ($client) {
            // Récupérer les dettes du client sans détails
            $dettes = Dette::where('clientid', $id)->get(['id', 'date', 'montant']);
    
            return response()->json([
                'status' => 200,
                'data' => [
                    'client' => $client,
                    'dettes' => $dettes->isNotEmpty() ? $dettes : null
                ],
                'message' => 'Client trouvé',
            ], 200);
        } else {
            return response()->json([
                'status' => 411,
                'data' => null,
                'message' => 'Objet non trouvé',
            ], 411);
        }
    }

  
    public function showWithUser($id)
    {
        $client = Client::find($id);

        if ($client) {
            $user = $client->user;  // Assuming there's a relation defined in the Client model

            return response()->json([
                'status' => 200,
                'data' => [
                    'client' => $client,
                    'user' => $user
                ],
                'message' => 'Client trouvé',
            ], 200);
        } else {
            return response()->json([
                'status' => 411,
                'data' => null,
                'message' => 'Objet non trouvé',
            ], 411);
        }
    }
    public function update(ValidateClientUpdateRequest $request, $id)
    {
        $client = Client::findOrFail($id);
        $client->update($request->validated());
        return response()->json(['message' => 'Client mis à jour avec succès', 'client' => $client]);
    }

    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();
        return response()->json(['message' => 'Client supprimé avec succès']);
    }
    
}