<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Console\Command;

class RemoveOldAccounts extends Command
{
    protected $signature = 'process:removed-accounts-after-month';

    protected $description = 'Command description';

    private UserRepository $userRepository;

    public function __construct(
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
        parent::__construct();
    }

    public function handle(): void
    {
        $users = $this->userRepository->findDeletedUsers();

        /** @var User $user */
        foreach ($users as $user) {
            $user->forceDelete();
        }
    }
}
