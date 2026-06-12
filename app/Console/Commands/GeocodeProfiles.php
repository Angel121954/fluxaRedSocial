<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Profile;
use App\Services\LocationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GeocodeProfiles extends Command
{
    protected $signature = 'fluxa:geocode-profiles
                           {--dry-run : Solo mostrar cuántos se geocodificarían sin llamar a Nominatim}';

    protected $description = 'Geocodifica perfiles con país/ciudad pero sin lat/lng';

    public function handle(LocationService $locationService): void
    {
        $dryRun = (bool) $this->option('dry-run');

        $profiles = Profile::whereNotNull('country')
            ->whereNull('latitude')
            ->get();

        if ($profiles->isEmpty()) {
            $this->info('No hay perfiles pendientes de geocodificar.');
            return;
        }

        $this->info("Se encontraron {$profiles->count()} perfiles para geocodificar.");

        if ($dryRun) {
            $this->warn('Modo --dry-run: no se realizaron cambios.');
            return;
        }

        $bar = $this->output->createProgressBar($profiles->count());
        $bar->start();

        $success = 0;
        $failed = 0;

        foreach ($profiles as $profile) {
            try {
                $coords = $locationService->geocode($profile->country, $profile->city);

                if ($coords !== null) {
                    $profile->updateQuietly([
                        'latitude' => $coords['latitude'],
                        'longitude' => $coords['longitude'],
                    ]);
                    $success++;
                } else {
                    $failed++;
                    Log::warning('Geocoding fallido', [
                        'profile_id' => $profile->id,
                        'country' => $profile->country,
                        'city' => $profile->city,
                    ]);
                }
            } catch (\Throwable $e) {
                $failed++;
                Log::error('Error geocodificando perfil', [
                    'profile_id' => $profile->id,
                    'error' => $e->getMessage(),
                ]);
            }

            $bar->advance();

            // Respetar límite de 1 req/s de Nominatim
            usleep(1_100_000);
        }

        $bar->finish();
        $this->newLine();
        $this->info("Geocodificación completada: {$success} exitosos, {$failed} fallidos.");
    }
}
