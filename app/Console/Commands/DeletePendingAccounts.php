<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class DeletePendingAccounts extends Command
{
    protected $signature = 'users:delete-pending';
    protected $description = 'Elimina cuentas pendientes de eliminación';

    public function handle()
    {
        $users = User::where('status', 'pending_deletion')
            ->where('delete_at', '<=', now())
            ->get();

        foreach ($users as $user) {
            $user->profile()->delete();
            $user->delete();
        }

        $this->info("Se eliminaron {$users->count()} cuentas.");
    }
}
