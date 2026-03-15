<?php

namespace App\Console\Commands;

use App\Mail\AdvertPreparationMail;
use App\Repositories\AdvertRepository;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPreparationEmails extends Command
{
    protected $signature = 'adverts:send-preparation-emails';

    protected $description = 'Send preparation emails for adverts that just passed their order deadline';

    private $advertRepository;

    public function __construct(AdvertRepository $advertRepository)
    {
        parent::__construct();
        $this->advertRepository = $advertRepository;
    }

    public function handle()
    {
        try {
            // Get adverts that have just passed their order time and haven't had emails sent
            $adverts = $this->advertRepository->getExpiringAdverts();

            $processedCount = 0;
            $errorCount = 0;

            foreach ($adverts as $advert) {
                try {
                    // Double check we haven't sent an email already
                    if ($advert->preparation_email_sent) {
                        continue;
                    }

                    // Ensure the advert has required relationships
                    if (! $advert->cook || ! $advert->cook->user) {
                        Log::warning("Skipping preparation email for advert {$advert->getUuid()} - missing cook or user relationship");

                        continue;
                    }

                    $cook = $advert->cook;
                    $user = $cook->user;

                    // Use database transaction to ensure email sending and flag update are atomic
                    DB::transaction(function () use ($advert, $user, $cook) {
                        // Send preparation email
                        Mail::to($user->getEmail())->send(new AdvertPreparationMail($advert, $cook));

                        // Mark email as sent using repository method
                        $this->advertRepository->markPreparationEmailSent($advert->getUuid());
                    });

                    $processedCount++;

                } catch (\Exception $e) {
                    $errorCount++;
                    Log::error("Failed to send preparation email for advert {$advert->getUuid()}: ".$e->getMessage());
                }
            }

            if ($processedCount > 0 || $errorCount > 0) {
                Log::info("Preparation emails sent: {$processedCount}, Errors: {$errorCount}");
            }

            return 0;

        } catch (\Exception $e) {
            Log::error('Fatal error in SendPreparationEmails command: '.$e->getMessage());

            return 1;
        }
    }
}
