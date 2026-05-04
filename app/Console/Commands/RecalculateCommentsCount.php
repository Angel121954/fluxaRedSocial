<?php

namespace App\Console\Commands;

use App\Models\Project;
use Illuminate\Console\Command;

class RecalculateCommentsCount extends Command
{
    protected $signature = 'projects:recalculate-comments';
    protected $description = 'Recalculate comments_count for all projects based on actual comments';

    public function handle()
    {
        $projects = Project::all();
        $bar = $this->output->createProgressBar($projects->count());
        $bar->start();

        foreach ($projects as $project) {
            $actualCount = $project->comments()->count();
            if ($project->comments_count != $actualCount) {
                $project->update(['comments_count' => $actualCount]);
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Comments count recalculated successfully.');
    }
}
