<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanGuestUsers extends Command
{
    protected $signature = 'users:clean-guests
                           {--dry-run : Solo mostrar cuántos se eliminarían sin borrar}';

    protected $description = 'Elimina cuentas guest mayores a 24 horas';

    public function handle(): void
    {
        $dryRun = (bool) $this->option('dry-run');

        $query = User::where('status', 'temporal')
            ->where('provider', 'guest')
            ->where('created_at', '<', now()->subHours(24));

        $count = $query->count();

        if ($count === 0) {
            $this->info('No hay cuentas guest expiradas.');
            return;
        }

        if ($dryRun) {
            $this->info("[DRY-RUN] Se eliminarían {$count} cuenta(s) guest.");
            return;
        }

        $query->delete();

        $this->info("Se eliminaron {$count} cuenta(s) guest.");
        Log::info('Cuentas guest expiradas eliminadas', ['count' => $count]);
    }
}
