<?php

namespace App\Repositories;

use App\Models\Client;
use App\Models\Dette;
use App\Repositories\Contracts\ClientRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ClientRepository implements ClientRepositoryInterface
{
    public function all($active = null)
    {
        $query = Client::query();

        if ($active !== null) {
            $query->whereHas('user', function ($q) use ($active) {
                $q->where('active', $active === 'oui');
            });
        }

        return $query->get();
    }

    public function find($id)
    {
        return Client::find($id);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            return Client::create($data);
        });
    }

    public function update($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $client = Client::find($id);
            if ($client) {
                $client->update($data);
            }
            return $client;
        });
    }

    public function delete($id)
    {
        return DB::transaction(function () use ($id) {
            $client = Client::find($id);
            if ($client) {
                $client->delete();
            }
            return $client;
        });
    }

    public function searchByTelephone($telephone)
    {
        return Client::where('telephone_portable', $telephone)->first();
    }

    public function listDettes($id)
    {
        $client = Client::find($id);
        if ($client) {
            $dettes = Dette::where('client_id', $id)->get(['id', 'date', 'montant', 'montantRestant']);
            return ['client' => $client, 'dettes' => $dettes->isNotEmpty() ? $dettes : null];
        }
        return null;
    }

    public function showWithUser($id)
    {
        $client = Client::find($id);
        if ($client) {
            $user = $client->user;
            return ['client' => $client, 'user' => $user];
        }
        return null;
    }
}
