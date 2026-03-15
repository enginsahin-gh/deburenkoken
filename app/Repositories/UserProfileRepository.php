<?php

namespace App\Repositories;

use App\Dtos\UserProfileDto;
use App\Models\User;
use App\Models\UserProfile;

class UserProfileRepository
{
    private UserProfile $userProfile;

    public function __construct(UserProfile $userProfile)
    {
        $this->userProfile = $userProfile;
    }

    public function find(string $uuid): ?UserProfile
    {
        return $this->userProfile->find($uuid);
    }

    public function findByUserUuid(string $userUuid): ?UserProfile
    {
        return $this->userProfile->where('user_uuid', $userUuid)->first();
    }

    public function createOrUpdate(
        UserProfileDto $userProfileDto,
        User $user
    ): UserProfile {
        $userProfile = $this->userProfile->where('user_uuid', $user->getUuid())->first();

        if (! is_null($userProfile)) {
            return $this->update($userProfileDto, $userProfile);
        }

        return $this->userProfile->create([
            'firstname' => $userProfileDto->getFirstName(),
            'insertion' => $userProfileDto->getInsertion(),
            'lastname' => $userProfileDto->getLastname(),
            'phone_number' => $userProfileDto->getPhoneNumber(),
            'birthday' => $userProfileDto->getBirthDay(),
            'user_uuid' => $user->getUuid(),
        ]);
    }

    public function update(
        UserProfileDto $userProfileDto,
        UserProfile $userProfile
    ): UserProfile {
        $userProfile->update([
            'firstname' => $userProfileDto->getFirstName(),
            'insertion' => $userProfileDto->getInsertion(),
            'lastname' => $userProfileDto->getLastname(),
            'phone_number' => $userProfileDto->getPhoneNumber(),
            'birthday' => $userProfileDto->getBirthDay(),
        ]);

        return $userProfile->refresh();
    }

    public function updatePhone(
        User $user,
        ?string $phone,
    ): ?UserProfile {
        $userProfile = $this->findByUserUuid($user->getUuid());

        if (is_null($userProfile)) {
            return null;
        }

        if (! is_null($phone)) {
            $update['phone_number'] = $phone;
        }

        if (isset($update)) {
            $userProfile->update($update);
        }

        return $userProfile;
    }

    public function delete(
        string $userProfileUuid
    ): ?bool {
        return $this->find($userProfileUuid)?->delete();
    }
}
