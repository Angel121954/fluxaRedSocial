<?php

declare(strict_types=1);

namespace App\Http\Controllers\Jobs;

use App\Http\Controllers\Controller;
use App\Http\Requests\Job\BookmarkJobRequest;
use App\Http\Requests\Job\StoreJobOfferRequest;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $query = Job::active()->with('skills');

        // Búsqueda por keyword
        if ($q = $request->input('q')) {
            $query->where(function ($qry) use ($q) {
                $qry->where('title', 'like', "%{$q}%")
                    ->orWhere('company_name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        // Filtro por ubicación
        if ($location = $request->input('location')) {
            $query->where(function ($qry) use ($location) {
                $qry->where('country', 'like', "%{$location}%")
                    ->orWhere('city', 'like', "%{$location}%")
                    ->orWhere('location', 'like', "%{$location}%");
            });
        }

        // Filtro por modalidad
        if ($modality = $request->input('modality')) {
            $query->where('modality', $modality);
        }

        // Filtro por tags rápidos
        if ($tags = $request->input('tags')) {
            $tags = (array) $tags;
            foreach ($tags as $tag) {
                match ($tag) {
                    'remoto' => $query->where('modality', 'remoto'),
                    'tecnologia' => $query->whereHas('skills'),
                    default => null,
                };
            }
        }

        // Orden
        $sort = $request->input('sort', 'recent');
        match ($sort) {
            'salary' => $query->orderBy('salary_max', 'desc'),
            'relevant' => $query->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc'),
            default => $query->orderBy('created_at', 'desc'),
        };

        $jobs = $query->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('jobs._list', ['jobs' => $jobs])->render(),
                'nextPageUrl' => $jobs->nextPageUrl(),
            ]);
        }

        return view('jobs.index', compact('jobs'));
    }

    public function show($id)
    {
        $job = Job::with('skills')->findOrFail($id);

        return view('jobs.show', compact('job'));
    }

    public function bookmark(BookmarkJobRequest $request)
    {
        $job = Job::findOrFail($request->job_id);
        $result = Auth::user()->bookmarkedJobs()->toggle($job->id);

        return response()->json([
            'saved' => ! empty($result['attached']),
        ]);
    }

    public function saved()
    {
        $jobs = Auth::user()->bookmarkedJobs()->paginate(20);

        return view('jobs.saved', compact('jobs'));
    }

    public function store(StoreJobOfferRequest $request)
    {
        $modalityMap = [
            'remoto' => ['label' => 'Remoto', 'location_type' => 'remote'],
            'hibrido' => ['label' => 'Híbrido', 'location_type' => 'hybrid'],
            'presencial' => ['label' => 'Presencial', 'location_type' => 'onsite'],
        ];

        $modality = $modalityMap[$request->modality];

        $user = Auth::user();
        $profile = $user->profile;

        $job = Job::create([
            'user_id' => $user->id,
            'company_name' => $user->name,
            'title' => $request->title,
            'description' => $request->description,
            'requirements' => $request->requirements,
            'benefits' => $request->benefits,
            'application_url' => $request->application_url,
            'modality' => $request->modality,
            'modality_label' => $modality['label'],
            'location_type' => $modality['location_type'],
            'location' => $request->location,
            'country' => $profile?->country ?? $request->location,
            'city' => $profile?->city,
            'seniority' => $request->seniority,
            'salary_min' => $request->salary_min,
            'salary_max' => $request->salary_max,
            'whatsapp' => $request->whatsapp,
            'currency' => $request->currency ?? 'usd',
            'salary_currency' => strtoupper($request->currency ?? 'usd'),
            'status' => 'published',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Oferta publicada exitosamente',
            'job' => $job,
            'html' => view('jobs._card', ['job' => $job])->render(),
        ]);
    }

    public function create()
    {
        return view('jobs.create');
    }
}
