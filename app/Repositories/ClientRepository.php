<?php

namespace App\Repositories;

use App\Dtos\ClientDto;
use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Collection;

class ClientRepository
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function find(string $uuid): ?Client
    {
        return $this->client->find($uuid);
    }

    public function get(): Collection
    {
        return $this->client->get();
    }

    public function findByEmail(string $email): ?Client
    {
        return $this->client->where('email', $email)->first();
    }

    public function create(ClientDto $clientDto): Client
    {
        $client = $this->findByEmail($clientDto->getEmail());

        if (is_null($client)) {
            return $this->client->create([
                'name' => $clientDto->getName(),
                'email' => $clientDto->getEmail(),
                'phone_number' => $clientDto->getPhoneNumber(),
            ]);
        }

        return $this->update($client->getUuid(), $clientDto);
    }

    public function update(
        string $clientUuid,
        ClientDto $clientDto,
        ?User $user = null
    ): ?Client {
        $client = $this->find($clientUuid);

        if (is_null($client)) {
            return null;
        }

        $client->update([
            'name' => $clientDto->getName(),
            'email' => $clientDto->getEmail(),
            'phone_number' => $clientDto->getPhoneNumber(),
            'user_uuid' => $user?->getUuid(),
        ]);

        return $client;
    }

    public function delete(string $clientUuid): ?bool
    {
        return $this->find($clientUuid)?->delete();
    }
}
