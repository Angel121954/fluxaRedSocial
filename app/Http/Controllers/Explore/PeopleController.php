<?php

declare(strict_types=1);

namespace App\Http\Controllers\Explore;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\Technology;
use App\Services\PeopleDirectoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeopleController extends Controller
{
    public function __invoke(Request $request, PeopleDirectoryService $peopleDirectory)
    {
        $filters = $request->only(['q', 'technology', 'country', 'specialty', 'sort']);
        $viewer = Auth::user();

        return view('explore.people', [
            'people' => $peopleDirectory->paginate($viewer, $filters),
            'followingIds' => $viewer?->follows()->pluck('followed_id') ?? collect(),
            'technologies' => Technology::query()
                ->whereHas('users')
                ->orderBy('name')
                ->get(['id', 'name', 'slug']),
            'countries' => Profile::query()
                ->whereNotNull('country')
                ->where('country', '!=', '')
                ->distinct()
                ->orderBy('country')
                ->pluck('country'),
            'specialties' => Profile::SPECIALTY_LABELS,
            'filters' => $filters,
            'hasFilters' => collect($filters)
                ->except('sort')
                ->filter(fn ($value) => filled($value))
                ->isNotEmpty(),
        ]);
    }
}
