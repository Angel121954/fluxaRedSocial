<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('users:delete-pending')->daily();
        $schedule->command('users:delete-expired')->daily();
        $schedule->command('cloudinary:clean-orphans')->weeklyOn(0, '03:00');
        $schedule->command('notifications:weekly-summary')->weeklyOn(0, '08:00');
        $schedule->command('diary:clean')->everyMinute();
        $schedule->command('notifications:prune')->monthly();
        $schedule->command('badges:scan')->weekly();
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
