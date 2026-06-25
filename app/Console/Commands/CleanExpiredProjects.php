<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\ProjectMedia;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanExpiredProjects extends Command
{
    protected $signature   = 'projects:cleanup {--days=7 : Días desde el soft-delete para forzar eliminación}';
    protected $description = 'Elimina permanentemente proyectos soft-deleteados con más de N días de antigüedad';

    public function handle(): void
    {
        $days = (int) $this->option('days');

        $projects = Project::onlyTrashed()
            ->where('deleted_at', '<=', now()->subDays($days))
            ->get();

        if ($projects->isEmpty()) {
            $this->info('No hay proyectos pendientes de eliminación definitiva.');

            return;
        }

        $cloudinary = new \Cloudinary\Cloudinary(config('cloudinary.cloud_url'));
        $bar = $this->output->createProgressBar($projects->count());
        $bar->start();

        foreach ($projects as $project) {
            try {
                $mediaItems = ProjectMedia::where('project_id', $project->id)->get();

                foreach ($mediaItems as $media) {
                    if ($media->public_id && str_contains($media->media_url, 'cloudinary.com')) {
                        try {
                            $cloudinary->uploadApi()->destroy($media->public_id);
                        } catch (\Exception $e) {
                            Log::warning('No se pudo eliminar media de Cloudinary', [
                                'project_id' => $project->id,
                                'public_id'  => $media->public_id,
                                'error'      => $e->getMessage(),
                            ]);
                        }
                    }
                }

                $project->forceDelete();

                $this->line(" Proyecto #{$project->id} eliminado.");
                Log::info('Proyecto eliminado definitivamente', [
                    'project_id' => $project->id,
                    'title'      => $project->title,
                ]);
            } catch (\Exception $e) {
                $this->error(" Error eliminando proyecto #{$project->id}: {$e->getMessage()}");
                Log::error('Error eliminando proyecto permanentemente', [
                    'project_id' => $project->id,
                    'error'      => $e->getMessage(),
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Limpieza de proyectos completada.');
    }
}
