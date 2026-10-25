<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FiltroDashboardRequest;
use App\Services\MetricasDashboardService;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(FiltroDashboardRequest $request, MetricasDashboardService $metricas): View
    {
        $datos = $request->validated();
        $desde = isset($datos['desde']) ? Carbon::parse($datos['desde'])->startOfDay() : null;
        $hasta = isset($datos['hasta']) ? Carbon::parse($datos['hasta'])->endOfDay() : null;

        $etiquetaRango = match (true) {
            $desde && $hasta => $desde->translatedFormat('d M').' – '.$hasta->translatedFormat('d M Y'),
            (bool) $desde => 'Desde '.$desde->translatedFormat('d M Y'),
            (bool) $hasta => 'Hasta '.$hasta->translatedFormat('d M Y'),
            default => 'Todo el tiempo',
        };

        return view('admin.index', [
            ...$metricas->obtener($desde, $hasta),
            'etiquetaRango' => $etiquetaRango,
            'desde' => $desde,
            'hasta' => $hasta,
        ]);
    }
}
