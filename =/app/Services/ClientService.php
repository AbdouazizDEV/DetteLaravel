<?php

namespace App\Services;

use App\Repositories\Contracts\ClientRepositoryInterface;
use App\Services\Contracts\ClientServiceInterface;
use Illuminate\Support\Facades\Storage;

class ClientService implements ClientServiceInterface
{
    protected $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    public function all($active = null)
    {
        $clients = $this->clientRepository->all($active);
        return $this->convertImagesToBase64($clients);
    }

    public function find($id)
    {
        $client = $this->clientRepository->find($id);
        return $this->convertImageToBase64($client);
    }

    public function create(array $data)
    {
        return $this->clientRepository->create($data);
    }

    public function update($id, array $data)
    {
        return $this->clientRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->clientRepository->delete($id);
    }

    public function searchByTelephone($telephone)
    {
        $client = $this->clientRepository->searchByTelephone($telephone);
        return $this->convertImageToBase64($client);
    }

    public function listDettes($id)
    {
        $result = $this->clientRepository->listDettes($id);
        if ($result) {
            $result['client'] = $this->convertImageToBase64($result['client']);
        }
        return $result;
    }

    public function showWithUser($id)
    {
        $result = $this->clientRepository->showWithUser($id);
        if ($result) {
            $result['client'] = $this->convertImageToBase64($result['client']);
        }
        return $result;
    }

    private function convertImageToBase64($client)
    {
        if ($client && $client->photo) {
            $path = str_replace(url('/'), '', $client->photo);
            if (Storage::exists($path)) {
                $imageData = Storage::get($path);
                $client->photo = base64_encode($imageData);
            } else {
                $client->photo = null; // Si le fichier n'existe pas, définir photo à null
            }
        }
        return $client;
    }

    private function convertImagesToBase64($clients)
    {
        foreach ($clients as $client) {
            $this->convertImageToBase64($client);
        }
        return $clients;
    }
}
