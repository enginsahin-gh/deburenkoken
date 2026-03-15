<?php

namespace App\Console\Commands;

use App\Models\Banking;
use Illuminate\Console\Command;

class ResetIbanChangeLimit extends Command
{
    protected $signature = 'iban:reset-limit';

    protected $description = 'Reset the IBAN change limit for testing';

    public function handle()
    {
        $userUuid = $this->ask('Enter the user UUID to reset IBAN change history:');

        IbanChangeHistory::where('user_uuid', $userUuid)->delete();

        $this->info('IBAN change history has been reset for testing.');
    }
}
