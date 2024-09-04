<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Facades\ClientServiceFacade;
use App\Http\Requests\ValidateClientPostRequest;
use App\Http\Requests\ValidateClientUpdateRequest;
use App\Http\Requests\ValidateUserPostRequest;
use Illuminate\Support\Facades\Validator;
use App\Models\Client;
use App\Services\Contracts\ClientServiceInterface;
class ClientController extends Controller
{
     
    protected $clientService;

    
    public function __construct(ClientServiceInterface $clientService)
    {
        $this->clientService = $clientService;
        $this->middleware('api.response');
        
    }
    public function index(Request $request)
    {
        $clients = $this->clientService->getAllClients($request);
        
        return response()->json([
            'status' => 200,
            'data' => $clients,
            'message' => 'Clients retrieved successfully'
        ]);
    }

    public function show($id)
    {
        $client = $this->clientService->getClientById($id);

        if (!$client) {
            return response()->json([
                'status' => 404,
                'message' => 'Client not found'
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => $client,
            'message' => 'Client retrieved successfully'
        ]);
    }

    public function searchByTelephone($telephone)
    {
        $client = ClientServiceFacade::searchByTelephone($telephone);
        return response()->json($client);
    }

  

    public function store(Request $request)
    {
        $client = $this->clientService->storeClient($request->all());
        return response()->json([
            'status' => 200,
            'data' => $client,
            'message' => 'Success'
        ]);
    }

    public function attachUser(Request $request)
    {
        $client = $this->clientService->attachUserToClient($request->client_id, $request->user);
        return response()->json([
            'status' => 200,
            'data' => $client,
            'message' => 'User attached successfully'
        ]);
    }
    

    public function listDettes($id)
    {
        $result = ClientServiceFacade::listDettes($id);
        return response()->json($result);
    }

    public function showWithUser($id)
    {
        $result = ClientServiceFacade::showWithUser($id);
        return response()->json($result);
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
