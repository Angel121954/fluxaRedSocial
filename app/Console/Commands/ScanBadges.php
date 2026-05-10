<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\BadgeService;
use Illuminate\Console\Command;

class ScanBadges extends Command
{
    protected $signature = 'badges:scan {--user= : Escanear un usuario específico por ID}';

    protected $description = 'Evalúa y otorga badges pendientes a los usuarios';

    public function handle(BadgeService $badgeService): void
    {
        $userId = $this->option('user');

        if ($userId) {
            $user = User::find($userId);
            if (! $user) {
                $this->error("Usuario #{$userId} no encontrado.");

                return;
            }

            $badgeService->scanUser($user);
            $this->info("Badges escaneados para usuario #{$userId}.");

            return;
        }

        $awarded = $badgeService->scanAll();
        $this->info("Escaneo completado. {$awarded} nuevos badges otorgados.");
    }
}
