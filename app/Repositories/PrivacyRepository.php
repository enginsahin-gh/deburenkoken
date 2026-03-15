<?php

namespace App\Repositories;

use App\Dtos\PrivacyDto;
use App\Models\Privacy;

class PrivacyRepository
{
    private Privacy $privacy;

    public function __construct(Privacy $privacy)
    {
        $this->privacy = $privacy;
    }

    public function find(string $privacyUuid): ?Privacy
    {
        return $this->privacy->find($privacyUuid);
    }

    public function findByUserUuid(string $userUuid): ?Privacy
    {
        return $this->privacy->where('user_uuid', $userUuid)->first();
    }

    public function create(
        PrivacyDto $privacyDto,
        string $userUuid
    ): Privacy {
        return $this->privacy->create([
            'user_uuid' => $userUuid,
            'place' => $privacyDto->getPlace(),
            'street' => $privacyDto->getStreet(),
            'house_number' => $privacyDto->getHouseNumber(),
            'phone' => $privacyDto->getPhone(),
            'email' => $privacyDto->getEmail(),
            'sold_portions' => $privacyDto->getSoldPortions(),
        ]);
    }

    public function update(
        PrivacyDto $privacyDto,
        string $privacyUuid
    ): ?Privacy {
        $privacy = $this->find($privacyUuid);

        if (is_null($privacy)) {
            return null;
        }

        $privacy->update([
            'place' => $privacyDto->getPlace(),
            'street' => $privacyDto->getStreet(),
            'house_number' => $privacyDto->getHouseNumber(),
            'phone' => $privacyDto->getPhone(),
            'email' => $privacyDto->getEmail(),
            'sold_portions' => $privacyDto->getSoldPortions(),
        ]);

        return $privacy;
    }

    public function delete(string $privacyUuid): ?bool
    {
        return $this->find($privacyUuid)?->delete();
    }
}
