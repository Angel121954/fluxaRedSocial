<?php

namespace App\Http\Controllers\Jobs;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        $profile = Auth::user()->role !== 'guest' ? Profile::where('user_id', Auth::id())->first() : null;

        return view('jobs.index', compact('jobs', 'profile'));
    }

    public function show($id)
    {
        $job = Job::with('skills')->findOrFail($id);
        $profile = Auth::user()->role !== 'guest' ? Profile::where('user_id', Auth::id())->first() : null;

        return view('jobs.show', compact('job', 'profile'));
    }

    public function bookmark(Request $request)
    {
        $request->validate(['job_id' => 'required|exists:jobs,id']);

        $exists = DB::table('job_bookmarks')
            ->where('user_id', Auth::id())
            ->where('job_id', $request->job_id)
            ->exists();

        if ($exists) {
            DB::table('job_bookmarks')
                ->where('user_id', Auth::id())
                ->where('job_id', $request->job_id)
                ->delete();
            $saved = false;
        } else {
            DB::table('job_bookmarks')->insert([
                'user_id' => Auth::id(),
                'job_id' => $request->job_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $saved = true;
        }

        return response()->json(['saved' => $saved]);
    }

    public function saved()
    {
        $jobs = Auth::user()->bookmarkedJobs()->paginate(20);
        $profile = Profile::where('user_id', Auth::id())->first();

        return view('jobs.saved', compact('jobs', 'profile'));
    }

    public function create()
    {
        $profile = Profile::where('user_id', Auth::id())->first();

        return view('jobs.create', compact('profile'));
    }
}
