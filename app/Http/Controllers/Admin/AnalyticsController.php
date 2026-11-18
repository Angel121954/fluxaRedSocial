<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FiltroDashboardRequest;
use App\Services\FunnelMetricsService;
use Carbon\Carbon;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function __invoke(FiltroDashboardRequest $request, FunnelMetricsService $metrics): View
    {
        $datos = $request->validated();
        $desde = isset($datos['desde'])
            ? Carbon::parse($datos['desde'])->startOfDay()
            : now()->subDays(30)->startOfDay();
        $hasta = isset($datos['hasta'])
            ? Carbon::parse($datos['hasta'])->endOfDay()
            : now()->endOfDay();

        $etiquetaRango = match (true) {
            isset($datos['desde']) && isset($datos['hasta']) => $desde->translatedFormat('d M').' – '.$hasta->translatedFormat('d M Y'),
            isset($datos['desde']) => 'Desde '.$desde->translatedFormat('d M Y'),
            isset($datos['hasta']) => 'Hasta '.$hasta->translatedFormat('d M Y'),
            default => 'Últimos 30 días',
        };

        return view('admin.analytics.index', [
            ...$metrics->build($desde, $hasta),
            'etiquetaRango' => $etiquetaRango,
            'desde' => $desde,
            'hasta' => $hasta,
        ]);
    }
}
