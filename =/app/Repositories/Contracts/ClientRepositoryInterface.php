<?php

namespace App\Repositories\Contracts;
use App\Models\User;
use App\Models\Client;
interface ClientRepositoryInterface
{
    public function all($active = null);
    public function find($id);
    public function create(array $data): Client;
    public function update($id, array $data);
    public function delete($id);
    public function searchByTelephone($telephone);
    public function listDettes($id);
    public function showWithUser($id);
}
