<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Facades\ClientServiceFacade;
use App\Http\Requests\ValidateClientPostRequest;
use App\Http\Requests\ValidateClientUpdateRequest;
use App\Http\Requests\ValidateUserPostRequest;
use Illuminate\Support\Facades\Validator;
use App\Services\Contracts\UploadServiceInterface;

class ClientController extends Controller
{
    protected $uploadService;

    public function __construct(UploadServiceInterface $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function index(Request $request)
    {
        $active = $request->query('active');
        $clients = ClientServiceFacade::all($active);

        if ($clients->isEmpty()) {
            return response()->json([
                'status' => 200,
                'data' => null,
                'message' => 'Pas de clients trouvés'
            ]);
        }

        return response()->json([
            'status' => 200,
            'data' => $clients,
            'message' => 'Liste des clients récupérée avec succès.'
        ]);
    }

    public function show($id)
    {
        $client = ClientServiceFacade::find($id);
        return response()->json($client);
    }

    public function searchByTelephone($telephone)
    {
        $client = ClientServiceFacade::searchByTelephone($telephone);

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

    public function store(Request $request)
    {
        $client = null;
        $user = null;

        if ($request->has('client_id')) {
            $client = ClientServiceFacade::find($request->input('client_id'));

            if (!$client) {
                return response()->json(['message' => 'Client non trouvé'], 404);
            }

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

            $validatedClientData = $clientValidator->validated();

            // Upload image
            if ($request->hasFile('image')) {
                $imagePath = $this->uploadService->upload(['image' => $request->file('image')]);
                $validatedClientData['photo'] = $imagePath;
            }

            $client = ClientServiceFacade::create($validatedClientData);

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

    public function listDettes($id)
    {
        $result = ClientServiceFacade::listDettes($id);

        if ($result) {
            return response()->json([
                'status' => 200,
                'data' => $result,
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
        $result = ClientServiceFacade::showWithUser($id);

        if ($result) {
            return response()->json([
                'status' => 200,
                'data' => $result,
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
        $client = ClientServiceFacade::update($id, $request->validated());
        return response()->json(['message' => 'Client mis à jour avec succès', 'client' => $client]);
    }

    public function destroy($id)
    {
        ClientServiceFacade::delete($id);
        return response()->json(['message' => 'Client supprimé avec succès']);
    }
}
    