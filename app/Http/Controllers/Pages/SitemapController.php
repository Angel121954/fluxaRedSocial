<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $xml = Cache::remember('sitemap.xml', 21600, function (): string {
            $urls = [
                ['loc' => route('landing'), 'lastmod' => null],
                ['loc' => route('about-fluxa'), 'lastmod' => null],
                ['loc' => route('terms'), 'lastmod' => null],
                ['loc' => route('privacy.policy'), 'lastmod' => null],
                ['loc' => route('contact.index'), 'lastmod' => null],
                ['loc' => route('salaries.index'), 'lastmod' => null],
            ];

            User::query()
                ->select('id', 'username', 'updated_at')
                ->visible()
                ->where('role', '!=', 'guest')
                ->whereNotNull('username')
                ->whereHas('profile', fn ($query) => $query->where('visibility', 'public'))
                ->orderByDesc('updated_at')
                ->limit(5000)
                ->each(function (User $user) use (&$urls): void {
                    $urls[] = [
                        'loc' => route('profile.show', $user->username),
                        'lastmod' => $user->updated_at?->toAtomString(),
                    ];
                });

            Project::query()
                ->select('id', 'slug', 'updated_at')
                ->discoverable()
                ->orderByDesc('updated_at')
                ->limit(5000)
                ->each(function (Project $project) use (&$urls): void {
                    $urls[] = [
                        'loc' => route('projects.show', $project),
                        'lastmod' => $project->updated_at?->toAtomString(),
                    ];
                });

            return view('sitemap', compact('urls'))->render();
        });

        return response(
            '<?xml version="1.0" encoding="UTF-8"?>'."\n".$xml,
            200,
            ['Content-Type' => 'application/xml; charset=UTF-8'],
        );
    }
}
