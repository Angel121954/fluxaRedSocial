<?php

declare(strict_types=1);

namespace App\View\Composers;

use App\Models\Technology;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class TopTechnologiesComposer
{
    public function compose(View $view): void
    {
        $technologies = Cache::remember('top_technologies', 600, function () {
            return Technology::withCount([
                'projects' => fn ($query) => $query->discoverable(),
            ])
                ->whereHas('projects', fn ($query) => $query->discoverable())
                ->orderByDesc('projects_count')
                ->limit(15)
                ->get();
        });

        $view->with('topTechnologies', $technologies);
    }
}
