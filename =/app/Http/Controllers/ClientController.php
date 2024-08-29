<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Http\Requests\ValidateClientPostRequest;
use App\Http\Requests\ValidateClientUpdateRequest;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        
        $clients = QueryBuilder::for(Client::class)
        ->allowedFilters([
            AllowedFilter::partial('surnom'),    // Filtrage partiel par surnom
            AllowedFilter::partial('telephone_portable'), // Filtrage partiel par telephone_portable
            AllowedFilter::exact('user_id'),  // Filtrage exact par user_id
        ])
        ->allowedSorts([
            'surnom', 'telephone_portable', 'created_at'     // Trie par nom, prénom ou date de création
        ])
        ->with('user')                        // Eager Loading pour la relation 'user'
        ->paginate(10);                       // Pagination

    // Retourne une collection paginée sous forme de JSON
    return response()->json($clients);
    }

    public function show($id)
    {
        
        /* return response()->json($client); */
        $client = QueryBuilder::for(Client::class)
        ->with('user')                        // Eager Loading pour la relation 'user'
        ->findOrFail($id);

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
