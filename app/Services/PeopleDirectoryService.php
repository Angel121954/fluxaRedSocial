<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class PeopleDirectoryService
{
    private const PER_PAGE = 12;

    public function paginate(?User $viewer, array $filters): LengthAwarePaginator
    {
        $sort = $filters['sort'] ?? 'relevant';
        $sharedTechnologyIds = $viewer?->technologies()->pluck('technologies.id') ?? collect();

        $query = User::query()
            ->select(['users.id', 'users.name', 'users.username', 'users.created_at'])
            ->visible()
            ->where('users.role', '!=', 'guest')
            ->where('users.account_type', 'developer')
            ->whereHas('profile', fn (Builder $query) => $query->where('visibility', 'public'))
            ->with([
                'profile:id,user_id,avatar,specialty,country,city,bio',
                'technologies:id,name,slug',
            ])
            ->withCount([
                'followers',
                'projects as public_projects_count' => fn (Builder $query) => $query->where('privacy', 'public'),
            ]);

        if ($viewer !== null) {
            $query->where('users.id', '!=', $viewer->id);

            $blockedIds = $viewer->blockedUserIds();

            if ($blockedIds->isNotEmpty()) {
                $query->whereNotIn('users.id', $blockedIds);
            }
        }

        // Solo se calcula la afinidad cuando el orden la necesita y hay stack con qué comparar.
        if ($sort === 'relevant' && $sharedTechnologyIds->isNotEmpty()) {
            $query->withCount([
                'technologies as shared_technologies_count' => fn (Builder $query) => $query->whereIn('technologies.id', $sharedTechnologyIds),
            ]);
        }

        $this->applyFilters($query, $filters);
        $this->applySorting($query, $sort, $sharedTechnologyIds->isNotEmpty());

        return $query->paginate(self::PER_PAGE)->withQueryString();
    }

    private function applyFilters(Builder $query, array $filters): void
    {
        $search = trim((string) ($filters['q'] ?? ''));

        if ($search !== '') {
            $query->where(function (Builder $query) use ($search): void {
                $query->where('users.name', 'like', "%{$search}%")
                    ->orWhere('users.username', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['technology'])) {
            $query->whereHas('technologies', fn (Builder $query) => $query->where('technologies.slug', $filters['technology']));
        }

        if (! empty($filters['country'])) {
            $query->whereHas('profile', fn (Builder $query) => $query->where('country', $filters['country']));
        }

        if (! empty($filters['specialty'])) {
            $query->whereHas('profile', fn (Builder $query) => $query->where('specialty', $filters['specialty']));
        }
    }

    private function applySorting(Builder $query, string $sort, bool $hasSharedTechnologies): void
    {
        if ($sort === 'recent') {
            $query->orderByDesc('users.created_at');

            return;
        }

        if ($sort === 'followers') {
            $query->orderByDesc('followers_count')
                ->orderByDesc('public_projects_count')
                ->orderByDesc('users.created_at');

            return;
        }

        if ($hasSharedTechnologies) {
            $query->orderByDesc('shared_technologies_count');
        }

        $query->orderByDesc('followers_count')
            ->orderByDesc('public_projects_count')
            ->orderByDesc('users.created_at');
    }
}
