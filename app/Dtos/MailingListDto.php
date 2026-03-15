<?php

namespace App\Dtos;

use App\Models\Client;
use App\Models\Cook;

class MailingListDto
{
    private Cook $cook;

    private Client $client;

    public function __construct(
        Cook $cook,
        Client $client
    ) {
        $this->cook = $cook;
        $this->client = $client;
    }

    public function getCook(): Cook
    {
        return $this->cook;
    }

    public function getClient(): Client
    {
        return $this->client;
    }
}
