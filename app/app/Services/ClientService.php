<?php

namespace App\Services;

use App\Models\Client;

class ClientService
{
    protected EntityKeyService $entityKeyService;

    public function __construct(EntityKeyService $entityKeyService)
    {
        $this->entityKeyService = $entityKeyService;
    }

    public function getAllClients()
    {
        return Client::withCount('projects')->orderBy('name')->get();
    }

    public function getActiveClientsList()
    {
        return Client::orderBy('name')->get();
    }

    public function createClient(array $data): Client
    {
        $client = Client::create($data);
        $this->entityKeyService->generateClientKey($client);
        return $client;
    }

    public function updateClient(Client $client, array $data): Client
    {
        $client->update($data);
        if (isset($data['name'])) {
            $this->entityKeyService->generateClientKey($client);
        }
        return $client;
    }

    public function deleteClient(Client $client): void
    {
        if ($client->id === 1) {
            throw new \Exception('Cannot delete the root SELF client.');
        }
        $client->delete();
    }
}
