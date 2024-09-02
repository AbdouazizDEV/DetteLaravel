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

use Spatie\QueryBuilder\AllowedSort;
use Illuminate\Support\Facades\Auth;


class ClientController extends Controller
{
     /**
     * @OA\Get(
     *     path="/v1/clients",
     *     summary="Récupérer tous les clients",
     *     description="Permet de récupérer la liste de tous les clients avec filtrage, tri et pagination",
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Numéro de la page",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Tri des résultats",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="filter",
     *         in="query",
     *         description="Filtrage des résultats",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des clients",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="client@example.com")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non autorisé"
     *     )
     * )
     */
    
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

    /**
     * @OA\Get(
     *     path="/v1/clients/{id}",
     *     summary="Récupérer un client spécifique",
     *     description="Permet de récupérer un client par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Client récupéré",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="client@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Client non trouvé"
     *     )
     * )
     */

    public function show($id)
    {
        
        /* return response()->json($client); */
        $client = QueryBuilder::for(Client::class)
        ->with('user')                        // Eager Loading pour la relation 'user'
        ->findOrFail($id);

        return response()->json($client);
    }

       /**
     * @OA\Get(
     *     path="/v1/clients/telephone",
     *     summary="Rechercher un client par numéro de téléphone",
     *     description="Permet de rechercher un client par numéro de téléphone (partial search)",
     *     @OA\Parameter(
     *         name="telephone",
     *         in="query",
     *         description="Numéro de téléphone",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Client trouvé",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="client@example.com"),
     *             @OA\Property(property="telephone", type="string", example="123-456-7890")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Client non trouvé"
     *     )
     * )
     */

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
    
 /**
     * @OA\Post(
     *     path="/v1/clients",
     *     summary="Ajouter un nouveau client",
     *     description="Permet d'ajouter un nouveau client",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="client@example.com"),
     *             @OA\Property(property="telephone", type="string", example="123-456-7890")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Client créé",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="client@example.com"),
     *             @OA\Property(property="telephone", type="string", example="123-456-7890")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Échec de la création du client"
     *     )
     * )
     */

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
    /**
     * @OA\Post(
     *     path="/v1/clients/{id}/dettes",
     *     summary="Récupérer les dettes d'un client",
     *     description="Permet de récupérer les dettes d'un client par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dettes du client récupérées",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="amount", type="number", example=100.00),
     *                 @OA\Property(property="due_date", type="string", example="2023-12-31")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Client non trouvé"
     *     )
     * )
     */

    public function listDettes($id)
    {
        $client = Client::find($id);
    
        if ($client) {
            // Récupérer les dettes du client sans détails
            $dettes = Dette::where('client_id', $id)->get(['id', 'date', 'montant', 'montantRestant']);
    
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

    /**
     * @OA\Post(
     *     path="/v1/clients/{id}/user",
     *     summary="Récupérer un client avec son user",
     *     description="Permet de récupérer un client avec son user par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Client avec son user récupéré",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="client@example.com"),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John User"),
     *                 @OA\Property(property="email", type="string", example="user@example.com")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Client non trouvé"
     *     )
     * )
     */

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

    /**
     * @OA\Put(
     *     path="/v1/clients/{id}",
     *     summary="Mettre à jour un client spécifique",
     *     description="Permet de mettre à jour un client par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="client@example.com"),
     *             @OA\Property(property="telephone", type="string", example="123-456-7890")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Client mis à jour",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="client@example.com"),
     *             @OA\Property(property="telephone", type="string", example="123-456-7890")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Client non trouvé"
     *     )
     * )
     */

    public function update(ValidateClientUpdateRequest $request, $id)
    {
        $client = Client::findOrFail($id);
        $client->update($request->validated());
        return response()->json(['message' => 'Client mis à jour avec succès', 'client' => $client]);
    }

    /**
     * @OA\Delete(
     *     path="/v1/clients/{id}",
     *     summary="Supprimer un client spécifique",
     *     description="Permet de supprimer un client par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Client supprimé"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Client non trouvé"
     *     )
     * )
     */
    
    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();
        return response()->json(['message' => 'Client supprimé avec succès']);
    }
    
}
