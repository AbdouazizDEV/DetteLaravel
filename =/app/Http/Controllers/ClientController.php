<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Facades\ClientServiceFacade;
use App\Http\Requests\ValidateClientUpdateRequest;
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
        return response($clients, 200);
    }

    public function show($id)
    {
        $client = $this->clientService->getClientById($id);

        if (!$client) {
            return response(['message' => 'Client not found'], 404);
        }

        return response($client, 200);
    }

    public function searchByTelephone($telephone)
    {
        $client = ClientServiceFacade::searchByTelephone($telephone);
        return response($client, 200);
    }

    public function store(Request $request)
    {
        $client = $this->clientService->storeClient($request->all());
        return response($client, 200);
    }

    public function attachUser(Request $request)
    {
        $client = $this->clientService->attachUserToClient($request->client_id, $request->user);
        return response($client, 200);
    }

    public function listDettes($id)
    {
        $result = ClientServiceFacade::listDettes($id);
        return response($result, 200);
    }

    public function showWithUser($id)
    {
        $result = ClientServiceFacade::showWithUser($id);
        return response($result, 200);
    }

    public function update(ValidateClientUpdateRequest $request, $id)
    {
        $client = ClientServiceFacade::update($id, $request->validated());
        return response(['message' => 'Client mis à jour avec succès', 'client' => $client], 200);
    }

    public function destroy($id)
    {
        ClientServiceFacade::delete($id);
        return response(['message' => 'Client supprimé avec succès'], 200);
    }
}
