<?php

declare(strict_types=1);

namespace App\View\Composers;

use App\Models\Technology;
use Illuminate\View\View;

class TopTechnologiesComposer
{
    public function compose(View $view): void
    {
        $view->with('topTechnologies', Technology::withCount('projects')
            ->orderByDesc('projects_count')
            ->limit(15)
            ->get());
    }
}
