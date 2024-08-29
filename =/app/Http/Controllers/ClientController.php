<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Http\Requests\ValidateClientPostRequest;
use App\Http\Requests\ValidateClientUpdateRequest;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        // Lazy Loading : Récupère uniquement les clients
        $clients = Client::paginate(10); // Pagination

        // Eager Loading : Récupère les clients avec leurs utilisateurs associés
        $clients = Client::with('user')->paginate(10);

        // Retourne une collection ou une ressource JSON
        return response()->json($clients);
    }

    public function show($id)
    {
        // Lazy Loading : Récupère uniquement le client
        $client = Client::findOrFail($id);

        // Eager Loading : Récupère le client avec son utilisateur associé
        $client = Client::with('user')->findOrFail($id);

        return response()->json($client);
    }

    public function store(ValidateClientPostRequest $request)
    {
        $client = Client::create($request->validated());
        return response()->json(['message' => 'Client ajouté avec succès', 'client' => $client], 201);
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
