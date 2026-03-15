<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeleteDatabaseBackup extends Command
{
    protected $signature = 'backup:delete-old';

    protected $description = 'Delete daily backups older than 3 months and hourly backups older than 4 weeks from Google Cloud Storage, excluding backups made on the first day of each month';

    public function handle()
    {
        // Unieke lock-key voor het opschoonproces
        $lockKey = 'database_backup_cleanup_in_progress';

        // Controleer of er al een opschoonproces loopt
        if (Cache::has($lockKey)) {
            $this->warn('Een ander opschoonproces is al bezig. Deze run wordt overgeslagen.');
            Log::info('Backup cleanup overgeslagen: een ander proces is al bezig.');

            return;
        }

        // Maak een lock aan die 60 minuten geldig is
        Cache::put($lockKey, true, 60 * 60);

        try {
            $storage = new StorageClient([
                'projectId' => env('GOOGLE_CLOUD_PROJECT_ID'),
                'keyFilePath' => storage_path('app/google-cloud-keys/deburenkoken-382621-d26bf51176d7.json'),
            ]);

            $bucket = $storage->bucket(env('GOOGLE_CLOUD_STORAGE_BUCKET_NAME'));
            $deletedCount = 0;

            // Get all objects in the bucket
            foreach ($bucket->objects() as $object) {
                $filename = $object->name();

                // Skip monthly backups (these are kept permanently)
                if (str_contains($filename, 'Backup-Monthly')) {
                    continue;
                }

                // Get the creation time of the backup
                $createTime = $object->info()['timeCreated'];
                $createDate = Carbon::parse($createTime);

                // Delete daily backups older than 3 months
                if (str_contains($filename, 'Backup-Daily') && $createDate->isPast()) {
                    if ($createDate->diffInMonths(Carbon::now()) > 3) {
                        $object->delete();
                        $deletedCount++;
                        Log::info("Deleted old daily backup: $filename");
                        $this->info("Deleted daily backup: $filename");
                    }
                }

                // Delete hourly backups older than 1 week
                if (str_contains($filename, 'Backup-Hourly') && $createDate->isPast()) {
                    if ($createDate->diffInDays(Carbon::now()) > 7) {
                        $object->delete();
                        $deletedCount++;
                        Log::info("Deleted old hourly backup: $filename");
                        $this->info("Deleted hourly backup: $filename");
                    }
                }
            }

            $this->info("Cleanup completed. Deleted $deletedCount old backups.");
            Log::info("Backup cleanup completed. Deleted $deletedCount old backups.");
        } catch (\Exception $e) {
            $this->error('Backup cleanup failed: '.$e->getMessage());
            Log::error('Backup cleanup failed: '.$e->getMessage());
        } finally {
            // Verwijder altijd de lock, zelfs bij fouten
            Cache::forget($lockKey);
        }
    }

    protected function getBackupBaseName($filename)
    {
        // Verwijder de timestamp uit de bestandsnaam om de basisnaam te krijgen
        return preg_replace('/(_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2})$/', '', $filename);
    }

    protected function isDatabaseEmpty()
    {
        $tables = DB::select('SHOW TABLES');
        foreach ($tables as $table) {
            $tableName = array_values((array) $table)[0];
            $count = DB::table($tableName)->count();
            if ($count > 0) {
                return false;
            }
        }

        return true;
    }
}
