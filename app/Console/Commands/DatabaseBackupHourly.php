<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DatabaseBackupHourly extends Command
{
    protected $signature = 'db:backup-hourly';

    protected $description = 'Automating Hourly Backups';

    public function handle()
    {
        // Unieke lock-key voor deze backup type
        $lockKey = 'database_backup_hourly_in_progress';

        // Controleer of er al een backup-proces loopt
        if (Cache::has($lockKey)) {
            $this->warn('Een ander hourly backup-proces is al bezig. Deze run wordt overgeslagen.');
            Log::info('Hourly backup overgeslagen: een ander proces is al bezig.');

            return;
        }

        // Maak een lock aan die 30 minuten geldig is (korter dan daily omdat het vaker draait)
        Cache::put($lockKey, true, 30 * 60);

        try {
            // Controleer de databaseverbinding
            DB::connection()->getPdo();

            // Maak een back-up van de database met nauwkeurigere timestamp (milliseconden)
            $backupFilename = 'Backup-Hourly-'.Carbon::now()->format('Y-m-d_H-i-s-v').'.sql.gz';
            $backupContent = $this->generateDatabaseBackup();

            if (! $backupContent) {
                throw new \Exception('Backup kon niet worden gegenereerd');
            }

            // Upload de back-up naar Google Cloud Storage
            $this->uploadToGoogleCloudStorage($backupFilename, $backupContent);

            $this->info('Database backup succesvol aangemaakt en geüpload naar Google Cloud Storage!');
            Log::info("Hourly database backup succesvol: $backupFilename");
        } catch (\Exception $e) {
            $this->error('Database backup mislukt: '.$e->getMessage());
            Log::error('Hourly database backup mislukt: '.$e->getMessage());
        } finally {
            // Verwijder altijd de lock, zelfs bij fouten
            Cache::forget($lockKey);
        }
    }

    protected function generateDatabaseBackup()
    {
        // Voer een MySQL-opdracht uit om een back-up van de database te maken (met shell escaping)
        $process = proc_open('mysqldump --user='.escapeshellarg(env('DB_USERNAME')).
                             ' --password='.escapeshellarg(env('DB_PASSWORD')).
                             ' --host='.escapeshellarg(env('DB_HOST')).' '.
                             escapeshellarg(env('DB_DATABASE')).' | gzip', [
                                 0 => ['pipe', 'r'],
                                 1 => ['pipe', 'w'],
                                 2 => ['pipe', 'w'],
                             ], $pipes);

        // Controleer of het proces correct is gestart
        if (is_resource($process)) {
            $backupContent = stream_get_contents($pipes[1]);
            $errorOutput = stream_get_contents($pipes[2]);

            fclose($pipes[1]);
            fclose($pipes[2]);
            $exitCode = proc_close($process);

            if ($exitCode !== 0) {
                Log::error("Database backup command failed: $errorOutput");

                return null;
            }

            return $backupContent;
        }

        return null;
    }

    protected function uploadToGoogleCloudStorage($filename, $content)
    {
        $storage = new StorageClient([
            'projectId' => env('GOOGLE_CLOUD_PROJECT_ID'),
            'keyFilePath' => storage_path('app/google-cloud-keys/deburenkoken-382621-d26bf51176d7.json'),
        ]);
        // Upload de back-up naar Google Cloud Storage
        $bucket = $storage->bucket(env('GOOGLE_CLOUD_STORAGE_BUCKET_NAME'));
        $object = $bucket->upload($content, [
            'name' => $filename,
        ]);

        return $object;
    }
}
