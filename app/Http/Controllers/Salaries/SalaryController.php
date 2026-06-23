<?php

declare(strict_types=1);

namespace App\Http\Controllers\Salaries;

use App\Http\Controllers\Controller;
use App\Http\Requests\Salary\StoreSalaryReportRequest;
use App\Models\SalaryReport;
use App\Models\Technology;
use App\Services\LocationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SalaryController extends Controller
{
    public function __construct(
        protected LocationService $locationService,
    ) {}

    public function index(): View
    {
        $countries = $this->locationService->getCountries();
        $technologies = Technology::orderBy('name')->get();
        $seniorities = [
            'junior' => 'Junior',
            'mid' => 'Mid-Level',
            'senior' => 'Senior',
            'lead' => 'Lead / Architect',
        ];

        $stats = $this->buildStats();

        return view('salaries.index', compact(
            'countries',
            'technologies',
            'seniorities',
            'stats',
        ));
    }

    public function data(Request $request): JsonResponse
    {
        $query = SalaryReport::with('technologies');

        if ($country = $request->get('country')) {
            $query->where('country', $country);
        }
        if ($seniority = $request->get('seniority')) {
            $query->where('seniority', $seniority);
        }
        if ($modality = $request->get('modality')) {
            $query->where('modality', $modality);
        }
        if ($technologyId = $request->get('technology_id')) {
            $query->whereHas('technologies', fn ($q) => $q->where('technology_id', $technologyId));
        }
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('country', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%");
            });
        }

        $reports = $query->latest()->paginate(20);

        $reports->getCollection()->transform(function ($report) {
            return [
                'id' => $report->id,
                'country' => $report->country,
                'city' => $report->city,
                'seniority' => $report->seniority,
                'experience_years' => $report->experience_years,
                'salary_usd' => $report->salary_usd,
                'modality' => $report->modality,
                'company' => $report->company,
                'technologies' => $report->technologies->pluck('name'),
                'created_at' => $report->created_at->diffForHumans(),
            ];
        });

        return response()->json($reports);
    }

    public function store(StoreSalaryReportRequest $request): JsonResponse
    {
        $report = SalaryReport::create([
            'user_id' => Auth::id(),
            'country' => $request->country,
            'city' => $request->city,
            'seniority' => $request->seniority,
            'experience_years' => $request->experience_years,
            'salary_usd' => $request->salary_usd,
            'modality' => $request->modality,
            'company' => $request->company,
        ]);

        if ($request->technologies) {
            $report->technologies()->sync($request->technologies);
        }

        return response()->json(['success' => true]);
    }

    private function buildStats(): array
    {
        $avg = SalaryReport::avg('salary_usd');
        $count = SalaryReport::count();
        $byCountry = SalaryReport::selectRaw('country, ROUND(AVG(salary_usd)) as avg, COUNT(*) as count')
            ->groupBy('country')
            ->orderByDesc('avg')
            ->get();
        $byTechnologyRaw = SalaryReport::selectRaw('technology_id, ROUND(AVG(salary_usd)) as avg, COUNT(*) as count')
            ->join('salary_report_technology', 'salary_reports.id', '=', 'salary_report_technology.salary_report_id')
            ->groupBy('technology_id')
            ->orderByDesc('avg')
            ->get();

        $techMap = Technology::whereIn('id', $byTechnologyRaw->pluck('technology_id'))
            ->get()
            ->keyBy('id');

        $byTechnology = $byTechnologyRaw->map(function ($item) use ($techMap) {
            $tech = $techMap->get($item->technology_id);

            return [
                'technology' => $tech?->name ?? 'Desconocida',
                'slug' => $tech?->slug,
                'icon_url' => $tech?->iconUrl(),
                'avg' => (int) $item->avg,
                'count' => (int) $item->count,
            ];
        });

        $reports = SalaryReport::with('technologies')
            ->latest()
            ->take(10)
            ->get();

        return compact('avg', 'count', 'byCountry', 'byTechnology', 'reports');
    }
}
