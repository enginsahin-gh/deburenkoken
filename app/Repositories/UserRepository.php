<?php

namespace App\Repositories;

use App\Constants\Roles;
use App\Dtos\CommonDto;
use App\Dtos\UserDto;
use App\Models\Banking;
use App\Models\CookProfileDescription;
use App\Models\User;
use App\Models\UserProfile;
use App\Support\SensitiveDataMasker;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class UserRepository
{
    private User $user;

    private CookProfileDescription $profileDescription;

    public function __construct(
        User $user,
        CookProfileDescription $profileDescription
    ) {
        $this->user = $user;
        $this->profileDescription = $profileDescription;
    }

    public function find(string $uuid): ?User
    {
        return $this->user->find($uuid);
    }

    public function findWithTrashed(string $uuid): ?User
    {
        return $this->user
            ->with('dish.adverts')
            ->withTrashed()
            ->find($uuid);
    }

    public function updateProfileDescription(
        CommonDto $commonDto,
        string $userUuid
    ): ?CookProfileDescription {
        $profile = $this->profileDescription->where('user_uuid', $userUuid)->first();

        if (is_null($profile)) {
            return $this->profileDescription->create([
                'user_uuid' => $userUuid,
                'description' => $commonDto->getItem(),
            ]);
        }

        $profile->update([
            'description' => $commonDto->getItem(),
        ]);

        return $profile;
    }

    public function updateLogin(User $user): User
    {
        $user->update([
            'updated_at' => Carbon::now(),
        ]);

        return $user;
    }

    public function findUserByEmail(string $email): ?User
    {
        return $this->user->where('email', '=', $email)->first();
    }

    public function findDeletedUserByEmail(string $email): ?User
    {
        return $this->user
            ->where('email', '=', $email)
            ->withTrashed()
            ->whereNotNull('deleted_at')
            ->first();
    }

    public function findUserByUserName(string $username): Collection
    {
        return $this->user
            ->where('username', '=', $username)
            ->whereHas('cook')
            ->with('cook')
            ->get();

    }

    public function findUserName(string $username): Collection
    {
        return $this->user
            ->where('username', '=', $username)
            ->get();

    }

    public function get(): Collection
    {
        return $this->user->get();
    }

    public function create(UserDto $userDto): User
    {
        $user = $this->user->create([
            'username' => $userDto->getUsername(),
            'email' => $userDto->getEmail(),
            'password' => $userDto->getPassword(),
            'avatar' => $userDto->getAvatar(),
            'blocked_by_admin' => $userDto->isBlockedByAdmin(),
        ]);

        $user->assignRole(Roles::COOK);

        // Neem oude orders over van verwijderde accounts met zelfde email
        $this->transferOldOrders($user);

        $this->transferOldImages($user);

        return $user;
    }

    public function transferOldImages(User $newUser): void
    {

        \App\Models\Image::whereHas('user', function ($query) use ($newUser) {
            $query->withTrashed()
                ->where('email', $newUser->email)
                ->where('uuid', '!=', $newUser->uuid);
        })
            ->where('main_picture', false)
            ->where('type_id', \App\Models\Image::PROFILE_IMAGE)
            ->whereNull('deleted_at')
            ->update(['user_uuid' => $newUser->uuid]);
    }

    private function transferOldOrders(User $newUser): void
    {
        // Vind oude orders van verwijderde accounts met zelfde email
        \App\Models\Order::withTrashed()
            ->whereHas('user', function ($query) use ($newUser) {
                $query->withTrashed()
                    ->where('email', $newUser->email)
                    ->whereNotNull('deleted_at');
            })
            ->update(['user_uuid' => $newUser->uuid]);
    }

    public function update(
        UserDto $userDto,
        string $userUuid
    ): ?User {
        $user = $this->find($userUuid);

        if (is_null($user)) {
            return null;
        }

        $user->update([
            'username' => $userDto->getUsername(),
            'email' => $userDto->getEmail(),
            'password' => $userDto->getPassword(),
            'avatar' => $userDto->getAvatar(),
            'blocked_by_admin' => $userDto->isBlockedByAdmin(),
        ]);

        return $user;
    }

    public function newEmail(
        string $email,
        string $userUuid
    ): ?User {
        $user = $this->find($userUuid);

        if (is_null($user)) {
            return null;
        }

        $user->update([
            'email' => $email,
            'email_verified_at' => null,
        ]);

        return $user;
    }

    public function delete(string $userUuid): ?bool
    {
        $user = $this->find($userUuid);

        if (is_null($user)) {
            return null;
        }

        $user->update(['email_verified_at' => null]);

        return $user->delete();
    }

    public function verifyEmailAddress(string $userUuid): ?User
    {
        $user = $this->find($userUuid);

        if (is_null($user)) {
            return null;
        }

        $user->markEmailAsVerified();

        return $user;
    }

    public function reactivateUser(
        User $user,
        string $password
    ): User {
        $user->restore();
        $user->update([
            'password' => $password,
            'email_verified_at' => null,
        ]);

        return $user;
    }

    public function getUsersForAdmin(?string $searchTerm = null): Collection
    {
        $query = $this->user
            ->whereHas('roles', function ($query) {
                $query->where('name', 'cook');
            });

        if (! is_null($searchTerm)) {
            $query->where('username', 'LIKE', '%'.$searchTerm.'%');
        }

        return $query
            ->withTrashed()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getAllUsersForAdmin(?string $searchTerm = null): Collection
    {
        $query = $this->user->query();

        if (! is_null($searchTerm)) {
            $query->where('username', 'LIKE', '%'.$searchTerm.'%');
        }

        return $query
            ->withTrashed()
            ->with(['roles', 'banking']) // Eager load relationships
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function blockUser(User $user): bool
    {
        return $user->update(['blocked_by_admin' => true]);
    }

    public function deblockUser(User $user): bool
    {
        return $user->update(['blocked_by_admin' => false]);
    }

    public function findDeletedUsers(): Collection
    {
        return $this->user
            ->where('deleted_at', '<', Carbon::now()->subMonth())
            ->withTrashed()
            ->get();
    }

    public function updateVerificationLinkExpiration(User $user, Carbon $expirationDate): void
    {
        $user->update(['verification_link_expires_at' => $expirationDate]);
    }

    public function findDeletedUserByUserNameWithEmail(string $username, string $email): ?User
    {
        return $this->user
            ->where('username', '=', $username)
            ->where('email', '=', $email)
            ->withTrashed()
            ->whereNotNull('deleted_at')
            ->first();
    }

    public function getAdminUsers()
    {
        return $this->user::join('model_has_roles', 'users.uuid', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', 'admin')
            ->select('users.email', 'users.username')
            ->get();
    }

    public function getUsersWithBankingDetails()
    {
        return User::with(['userProfile', 'banking'])
            ->get()
            ->map(function ($user) {
                return [
                    'user_uuid' => $user->uuid,
                    'username' => $user->username,
                    'email' => $user->email,
                    'first_name' => $user->userProfile->firstname ?? null,
                    'middle_name' => $user->userProfile->insertion ?? null,
                    'last_name' => $user->userProfile->lastname ?? null,
                    'birthday' => $user->userProfile->birthday ?? null,
                    'iban' => SensitiveDataMasker::mask($user->banking->iban ?? null),
                    'account_holder' => $user->banking->account_holder ?? null,
                ];
            });
    }

    public function getUsersWithBankingDetailsLike($query)
    {
        return User::with(['userProfile', 'banking'])
            ->where('username', 'like', '%'.$query.'%')
            ->get()
            ->map(function ($user) {
                return [
                    'user_uuid' => $user->uuid,
                    'username' => $user->username,
                    'email' => $user->email,
                    'first_name' => $user->userProfile->firstname ?? null,
                    'middle_name' => $user->userProfile->insertion ?? null,
                    'last_name' => $user->userProfile->lastname ?? null,
                    'birthday' => $user->userProfile->birthday ?? null,
                    'iban' => SensitiveDataMasker::mask($user->banking->iban ?? null),
                    'account_holder' => $user->banking->account_holder ?? null,
                ];
            });
    }
}
