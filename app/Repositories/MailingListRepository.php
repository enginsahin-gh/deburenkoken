<?php

namespace App\Repositories;

use App\Dtos\MailingListDto;
use App\Models\Client;
use App\Models\Cook;
use App\Models\MailingList;
use App\Repositories\ClientRepository;

class MailingListRepository
{
    private MailingList $mailingList;

    private ClientRepository $clientRepository;

    public function __construct(MailingList $mailingList, ClientRepository $clientRepository)
    {
        $this->mailingList = $mailingList;
        $this->clientRepository = $clientRepository; // Add this line to assign the property
    }

    public function create(MailingListDto $mailingListDto): MailingList
    {
        return $this->mailingList->create([
            'cook_uuid' => $mailingListDto->getCook()->getUuid(),
            'client_uuid' => $mailingListDto->getClient()->getUuid(),
        ]);
    }

    public function emailExistsInMailingList(string $email): bool
    {
        $user = $this->clientRepository->findByEMail($email);

        if (! $user) {
            return false;
        }

        $uuidExistsInMailingList = $this->mailingList
            ->where('client_uuid', $user->getUuid())
            ->exists();

        return $uuidExistsInMailingList;
    }

    public function unsubscribeClient(Cook $cook, Client $client): void
    {
        $this->mailingList
            ->where('cook_uuid', $cook->getUuid())
            ->where('client_uuid', $client->getUuid())
            ->delete();
    }
}
