<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DeleteExpiredUsers extends Command
{
    protected $signature   = 'users:delete-expired';
    protected $description = 'Elimina usuarios con delete_at vencido y su imagen de Cloudinary';

    public function handle(): void
    {
        $users = User::where('status', 'pending_deletion')
            ->whereNotNull('delete_at')
            ->where('delete_at', '<=', now())
            ->get();

        if ($users->isEmpty()) {
            $this->info('No hay usuarios pendientes de eliminación.');
            return;
        }

        $cloudinary = new \Cloudinary\Cloudinary(config('cloudinary.cloud_url'));

        foreach ($users as $user) {
            try {
                // Eliminar imagen de Cloudinary si existe
                $avatarUrl = $user->profile?->avatar;

                if ($avatarUrl && str_contains($avatarUrl, 'cloudinary.com')) {
                    $cloudinary->uploadApi()->destroy('avatares/user_' . $user->id);

                    Log::info('Avatar eliminado de Cloudinary', ['user_id' => $user->id]);
                }

                // Eliminar usuario (profile se borra en cascada)
                $user->forceDelete();

                $this->info("Usuario #{$user->id} eliminado.");
                Log::info('Usuario eliminado definitivamente', ['user_id' => $user->id]);
            } catch (\Exception $e) {
                $this->error("Error eliminando usuario #{$user->id}: {$e->getMessage()}");
                Log::error('Error en eliminación de usuario', [
                    'user_id' => $user->id,
                    'error'   => $e->getMessage(),
                ]);
            }
        }
    }
}
