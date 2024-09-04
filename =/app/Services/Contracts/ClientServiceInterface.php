<?php

namespace App\Services\Contracts;
use App\Models\Client;
use Illuminate\Http\Request;
interface ClientServiceInterface
{
    public function all($active = null);
    public function find($id);
    public function create(array $data);
    public function storeClient(array $data): Client;
    public function update($id, array $data);
    public function delete($id);
    public function searchByTelephone($telephone);
    public function listDettes($id);
    public function showWithUser($id);
    public function attachUserToClient(int $clientId, array $userData): Client;
    public function getAllClients(Request $request);
    public function getClientById($id): ?Client;
}