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

use Spatie\QueryBuilder\AllowedSort;
use Illuminate\Support\Facades\Auth;



class ClientController extends Controller
{
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
    

    public function show($id)
    {
        
        /* return response()->json($client); */
        $client = QueryBuilder::for(Client::class)
        ->with('user')                        // Eager Loading pour la relation 'user'
        ->findOrFail($id);

        return response()->json($client);
    }

    public function store(Request $request)
    {
        $client = null;
        $user = null;
    
        // Vérifiez si l'ID du client est fourni pour une mise à jour
        if ($request->has('client_id')) {
            // Recherchez le client dans la base de données
            $client = Client::find($request->input('client_id'));
    
            if (!$client) {
                return response()->json(['message' => 'Client non trouvé'], 404);
            }
    
            // Si un utilisateur est inclus dans la requête, validez les données de l'utilisateur
            if ($request->has('user')) {
                $userRequest = new ValidateUserPostRequest($request->input('user'));
    
                $userValidator = Validator::make(
                    $userRequest->input(),
                    $userRequest->rules(),
                    $userRequest->messages()
                );
    
                if ($userValidator->fails()) {
                    return response()->json([
                        'message' => 'Erreur de validation de l\'utilisateur',
                        'errors' => $userValidator->errors()
                    ], 411);
                }
    
                // Créer l'utilisateur
                $userData = $userValidator->validated();
                $userData['password'] = bcrypt($userData['password']);
                $user = \App\Models\User::create($userData);
    
                // Associez l'utilisateur au client
                $client->user_id = $user->id;
                $client->save();
            }
    
            return response()->json([
                'message' => 'Compte utilisateur ajouté au client existant avec succès',
                'data' => [
                    'client' => $client,
                    'user' => $user
                ]
            ], 201);
    
        } else {
            // Si client_id n'est pas fourni, alors c'est une nouvelle création de client
            $clientRequest = new ValidateClientPostRequest($request->all());
    
            $clientValidator = Validator::make(
                $clientRequest->input(),
                $clientRequest->rules(),
                $clientRequest->messages()
            );
    
            if ($clientValidator->fails()) {
                return response()->json([
                    'message' => 'Erreur de validation',
                    'errors' => $clientValidator->errors()
                ], 411);
            }
    
            // Créez le client
            $validatedClientData = $clientValidator->validated();
            $client = Client::create($validatedClientData);
    
            // Si un utilisateur est inclus, créez également l'utilisateur
            if ($request->has('user')) {
                $userRequest = new ValidateUserPostRequest($request->input('user'));
    
                $userValidator = Validator::make(
                    $userRequest->input(),
                    $userRequest->rules(),
                    $userRequest->messages()
                );
    
                if ($userValidator->fails()) {
                    return response()->json([
                        'message' => 'Erreur de validation de l\'utilisateur',
                        'errors' => $userValidator->errors()
                    ], 411);
                }
    
                $userData = $userValidator->validated();
                $userData['password'] = bcrypt($userData['password']);
                $user = \App\Models\User::create($userData);
    
                // Associez l'utilisateur au client
                $client->user_id = $user->id;
                $client->save();
            }
    
            return response()->json([
                'message' => 'Client enregistré avec succès',
                'data' => [
                    'client' => $client,
                    'user' => $user
                ]
            ], 201);
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
