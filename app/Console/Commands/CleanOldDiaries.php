<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Diary;
use Illuminate\Console\Command;

class CleanOldDiaries extends Command
{
    protected $signature = 'diary:clean';

    protected $description = 'Cierra diarios activos con más de 24h';

    public function handle(): void
    {
        $count = Diary::where('status', 'active')
            ->where('created_at', '<', now()->subHours(24))
            ->update(['status' => 'closed']);

        if ($count > 0) {
            $this->info("Se cerraron {$count} diario(s).");
        } else {
            $this->info('No hay diarios para cerrar.');
        }
    }
}
