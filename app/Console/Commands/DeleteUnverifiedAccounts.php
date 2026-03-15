<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;
use Mail;

class DeleteUnverifiedAccounts extends Command
{
    protected $signature = 'accounts:delete-unverified';

    protected $description = 'Delete unverified accounts older than 30 days';

    public function handle()
    {
        $expirationDate = Carbon::now()->subDays(30);

        // Raw SQL query to delete unverified accounts older than 30 days
        $deletedUsers = DB::table('users')
            ->whereNull('email_verified_at')
            ->where('created_at', '<=', $expirationDate)
            ->get();

        foreach ($deletedUsers as $user) {
            // Convert stdClass to User model
            $userModel = User::find($user->uuid);

            $this->sendNotification($userModel);
        }

        // Delete the users
        $deletedCount = DB::table('users')
            ->whereNull('email_verified_at')
            ->where('created_at', '<=', $expirationDate)
            ->delete();

        $this->info($deletedCount.' unverified accounts deleted successfully.');
    }

    private function sendNotification(User $user)
    {
        // Customize the email notification content
        $data = [
            'name' => $user->username, // Adjust this based on your user model
            // Add any other data you want to include in the email
        ];

        // Send the email
        Mail::send('emails.unverified-account', $data, function ($message) use ($user) {
            $message->to($user->email)->subject('Verificatie niet voltooid');
        });
    }
}
