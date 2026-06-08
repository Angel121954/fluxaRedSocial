<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Message;
use App\Models\ProjectMedia;
use App\Models\Suggestion;
use App\Models\User;
use Cloudinary\Cloudinary;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanCloudinaryOrphans extends Command
{
    protected $signature = 'cloudinary:clean-orphans
        {--dry-run : Solo listar huérfanos sin eliminarlos}';

    protected $description = 'Elimina imágenes de Cloudinary que ya no existen en la BD';

    protected Cloudinary $cloudinary;

    public function __construct()
    {
        parent::__construct();
        $this->cloudinary = new Cloudinary(config('cloudinary.cloud_url'));
    }

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $totalDeleted = 0;

        $totalDeleted += $this->cleanAvatars($dryRun);
        $totalDeleted += $this->cleanProjectMedia($dryRun);
        $totalDeleted += $this->cleanMessages($dryRun);
        $totalDeleted += $this->cleanSuggestions($dryRun);

        $this->newLine();
        $this->info("Proceso completado. {$totalDeleted} recursos eliminados.");
        Log::info('cloudinary:clean-orphans finalizado', ['deleted' => $totalDeleted, 'dry_run' => $dryRun]);

        return Command::SUCCESS;
    }

    protected function cleanAvatars(bool $dryRun): int
    {
        $this->info('── Avatares ──');
        $resources = $this->listResources('fluxa/avatares');

        if (empty($resources)) {
            $this->warn('No hay recursos en fluxa/avatares.');
            return 0;
        }

        $deleted = 0;

        foreach ($resources as $resource) {
            $publicId = $resource['public_id'];

            preg_match('/user_(\d+)$/', $publicId, $matches);
            if (!isset($matches[1])) {
                continue;
            }

            $userId = (int) $matches[1];
            $user = User::with('profile')->find($userId);

            $isOrphan = false;

            if (!$user) {
                $isOrphan = true;
                $reason = 'usuario no existe';
            } elseif (!$user->profile || !$user->profile->avatar) {
                $isOrphan = true;
                $reason = 'usuario sin avatar';
            } elseif (!str_contains($user->profile->avatar, $publicId)) {
                $isOrphan = true;
                $reason = 'avatar no coincide con Cloudinary';
            }

            if ($isOrphan) {
                $this->line("  [{$publicId}] {$reason}");
                if (!$dryRun) {
                    $this->destroy($publicId);
                }
                $deleted++;
            }
        }

        $this->info("  Avatares: {$deleted} huérfanos" . ($dryRun ? ' (simulado)' : ' eliminados'));

        return $deleted;
    }

    protected function cleanProjectMedia(bool $dryRun): int
    {
        $this->info('── Projects ──');
        $resources = $this->listResources('fluxa/projects');

        if (empty($resources)) {
            $this->warn('No hay recursos en fluxa/projects.');
            return 0;
        }

        $dbPublicIds = ProjectMedia::query()
            ->whereNotNull('public_id')
            ->pluck('public_id')
            ->map(fn(string $id) => trim($id))
            ->flip();

        $deleted = 0;

        foreach ($resources as $resource) {
            $publicId = $resource['public_id'];

            if (!isset($dbPublicIds[$publicId])) {
                $this->line("  [{$publicId}] no existe en project_media");
                if (!$dryRun) {
                    $this->destroy($publicId, $resource['resource_type'] ?? 'image');
                }
                $deleted++;
            }
        }

        $this->info("  Projects: {$deleted} huérfanos" . ($dryRun ? ' (simulado)' : ' eliminados'));

        return $deleted;
    }

    protected function cleanMessages(bool $dryRun): int
    {
        $this->info('── Messages ──');
        $resources = $this->listResources('fluxa/messages');

        if (empty($resources)) {
            $this->warn('No hay recursos en fluxa/messages.');

            return 0;
        }

        $dbPublicIds = Message::query()
            ->whereNotNull('public_id')
            ->pluck('public_id')
            ->map(fn(string $id) => trim($id))
            ->flip();

        $deleted = 0;

        foreach ($resources as $resource) {
            $publicId = $resource['public_id'];

            if (!isset($dbPublicIds[$publicId])) {
                $this->line("  [{$publicId}] no existe en messages");
                if (!$dryRun) {
                    $this->destroy($publicId, $resource['resource_type'] ?? 'image');
                }
                $deleted++;
            }
        }

        $this->info("  Messages: {$deleted} huérfanos" . ($dryRun ? ' (simulado)' : ' eliminados'));

        return $deleted;
    }

    protected function cleanSuggestions(bool $dryRun): int
    {
        $this->info('── Suggestions ──');
        $resources = $this->listResources('fluxa/suggestions');

        if (empty($resources)) {
            $this->warn('No hay recursos en fluxa/suggestions.');
            return 0;
        }

        $dbPublicIds = Suggestion::query()
            ->whereNotNull('image_path')
            ->get()
            ->map(function (Suggestion $suggestion) {
                $path = $suggestion->image_path;
                if (str_contains($path, 'cloudinary.com')) {
                    $parts = explode('/upload/', $path);
                    if (count($parts) === 2) {
                        return pathinfo($parts[1], PATHINFO_FILENAME);
                    }
                }
                return $path;
            })
            ->filter()
            ->flip();

        $deleted = 0;

        foreach ($resources as $resource) {
            $publicId = $resource['public_id'];

            if (!isset($dbPublicIds[$publicId])) {
                $this->line("  [{$publicId}] no existe en suggestions");
                if (!$dryRun) {
                    $this->destroy($publicId);
                }
                $deleted++;
            }
        }

        $this->info("  Suggestions: {$deleted} huérfanos" . ($dryRun ? ' (simulado)' : ' eliminados'));

        return $deleted;
    }

    protected function listResources(string $prefix): array
    {
        $all = [];
        $nextCursor = null;

        $assetTypes = ['image', 'video'];

        foreach ($assetTypes as $resourceType) {
            $nextCursor = null;

            do {
                $params = [
                    'type' => 'upload',
                    'prefix' => $prefix,
                    'max_results' => 500,
                    'resource_type' => $resourceType,
                ];

                if ($nextCursor) {
                    $params['next_cursor'] = $nextCursor;
                }

                $result = $this->cloudinary->adminApi()->assets($params);

                $resources = $result['resources'] ?? [];
                $all = array_merge($all, $resources);

                $nextCursor = $result['next_cursor'] ?? null;
            } while ($nextCursor);
        }

        return $all;
    }

    protected function destroy(string $publicId, string $resourceType = 'image'): void
    {
        try {
            $this->cloudinary->uploadApi()->destroy($publicId, [
                'resource_type' => $resourceType,
            ]);

            Log::info('Huérfano eliminado de Cloudinary', [
                'public_id' => $publicId,
                'resource_type' => $resourceType,
            ]);
        } catch (\Exception $e) {
            $this->warn("  Error al eliminar {$publicId}: {$e->getMessage()}");
            Log::warning('Error eliminando huérfano de Cloudinary', [
                'public_id' => $publicId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
