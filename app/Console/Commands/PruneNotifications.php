<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PruneNotifications extends Command
{
    protected $signature = 'notifications:prune
                           {--days=30 : Eliminar notificaciones leídas con más de N días}
                           {--dry-run : Solo mostrar cuántas se eliminarían sin borrar}';

    protected $description = 'Elimina notificaciones leídas antiguas para mantener la tabla limpia';

    public function handle(): void
    {
        $days   = (int) $this->option('days');
        $dryRun = (bool) $this->option('dry-run');

        $query = Notification::where('is_read', true)
            ->where('created_at', '<', now()->subDays($days));

        $count = $query->count();

        if ($count === 0) {
            $this->info("No hay notificaciones leídas con más de {$days} días.");
            return;
        }

        if ($dryRun) {
            $this->info("[DRY-RUN] Se eliminarían {$count} notificación(es) leída(s) con más de {$days} días.");
            return;
        }

        $query->delete();

        $this->info("Se eliminaron {$count} notificación(es) leída(s) con más de {$days} días.");
        Log::info('Notificaciones antiguas eliminadas', [
            'count' => $count,
            'days'  => $days,
        ]);
    }
}
