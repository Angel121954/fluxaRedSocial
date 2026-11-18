<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\AnalyticsEvent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PruneAnalyticsEvents extends Command
{
    protected $signature = 'analytics:prune
                           {--days=365 : Eliminar eventos con más de N días}
                           {--dry-run : Solo mostrar cuántos se eliminarían sin borrar}';

    protected $description = 'Elimina eventos de analítica antiguos para mantener la tabla ligera';

    public function handle(): void
    {
        $days = (int) $this->option('days');
        $dryRun = (bool) $this->option('dry-run');

        $query = AnalyticsEvent::where('created_at', '<', now()->subDays($days));

        $count = $query->count();

        if ($count === 0) {
            $this->info("No hay eventos con más de {$days} días.");

            return;
        }

        if ($dryRun) {
            $this->info("[DRY-RUN] Se eliminarían {$count} evento(s) con más de {$days} días.");

            return;
        }

        $query->delete();

        $this->info("Se eliminaron {$count} evento(s) con más de {$days} días.");
        Log::info('Eventos de analítica antiguos eliminados', [
            'count' => $count,
            'days' => $days,
        ]);
    }
}
