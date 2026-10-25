<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FiltroDashboardRequest;
use App\Models\Contact;
use App\Models\DiaryReport;
use App\Models\ProblemReport;
use App\Models\ProjectReport;
use App\Models\Suggestion;
use App\Models\User;
use App\Models\UserReport;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(FiltroDashboardRequest $request): View
    {
        $datos = $request->validated();
        $desde = isset($datos['desde']) ? Carbon::parse($datos['desde'])->startOfDay() : null;
        $hasta = isset($datos['hasta']) ? Carbon::parse($datos['hasta'])->endOfDay() : null;
        $rangoAnterior = $this->rangoAnterior($desde, $hasta);

        $totalUsuarios = $this->contarEnRango(User::query(), $desde, $hasta);
        $usuariosActivos = $this->contarEnRango(User::where('status', 'activo'), $desde, $hasta);
        $usuariosVerificados = $this->contarEnRango(User::whereNotNull('email_verified_at'), $desde, $hasta);
        $totalSugerencias = $this->contarEnRango(Suggestion::query(), $desde, $hasta);

        $reportesPorTipo = collect([
            'usuario' => $this->contarEnRango(UserReport::query(), $desde, $hasta),
            'proyecto' => $this->contarEnRango(ProjectReport::query(), $desde, $hasta),
            'diario' => $this->contarEnRango(DiaryReport::query(), $desde, $hasta),
            'problema' => $this->contarEnRango(ProblemReport::query(), $desde, $hasta),
        ]);

        $totalReportes = $reportesPorTipo->sum();

        $variaciones = [
            'totalUsuarios' => $this->variacion($totalUsuarios, $this->contarAnterior(User::query(), $rangoAnterior)),
            'usuariosActivos' => $this->variacion($usuariosActivos, $this->contarAnterior(User::where('status', 'activo'), $rangoAnterior)),
            'usuariosVerificados' => $this->variacion($usuariosVerificados, $this->contarAnterior(User::whereNotNull('email_verified_at'), $rangoAnterior)),
            'totalSugerencias' => $this->variacion($totalSugerencias, $this->contarAnterior(Suggestion::query(), $rangoAnterior)),
            'totalReportes' => $this->variacion($totalReportes, $this->contarReportesAnteriores($rangoAnterior)),
        ];

        $sugerenciasPorEstado = $this->aplicarRango(
            Suggestion::selectRaw('status, COUNT(*) as count')->groupBy('status'),
            $desde,
            $hasta,
        )->pluck('count', 'status');

        $crecimientoUsuarios = $this->aplicarRango(
            User::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count")
                ->groupBy('month')
                ->orderBy('month'),
            $desde,
            $hasta,
        )->get();

        $usuariosMasActivos = User::with('profile')
            ->withCount(['projects' => fn (Builder $consulta) => $this->aplicarRango($consulta, $desde, $hasta)])
            ->orderByDesc('projects_count')
            ->take(5)
            ->get();

        $usuariosRecientes = $this->aplicarRango(
            User::select('id', 'name', 'username', 'created_at'),
            $desde,
            $hasta,
        )->latest()->take(5)->get()->map(fn (User $usuario) => [
            'tipo' => 'usuario',
            'titulo' => 'Nuevo usuario registrado',
            'descripcion' => "{$usuario->name} se unió a Fluxa",
            'hora' => $usuario->created_at,
        ]);

        $sugerenciasRecientes = $this->aplicarRango(
            Suggestion::with('user'),
            $desde,
            $hasta,
        )->latest()->take(5)->get()->map(fn (Suggestion $sugerencia) => [
            'tipo' => 'sugerencia',
            'titulo' => 'Nueva sugerencia',
            'descripcion' => $sugerencia->description,
            'hora' => $sugerencia->created_at,
        ]);

        $contactosRecientes = $this->aplicarRango(
            Contact::where('readed', false),
            $desde,
            $hasta,
        )->latest()->take(5)->get()->map(fn (Contact $contacto) => [
            'tipo' => 'contacto',
            'titulo' => 'Nuevo mensaje de contacto',
            'descripcion' => "{$contacto->name}: {$contacto->message}",
            'hora' => $contacto->created_at,
        ]);

        $actividad = collect()
            ->merge($usuariosRecientes)
            ->merge($sugerenciasRecientes)
            ->merge($contactosRecientes)
            ->sortByDesc('hora')
            ->take(5)
            ->values();

        $etiquetaRango = match (true) {
            $desde && $hasta => $desde->translatedFormat('d M').' – '.$hasta->translatedFormat('d M Y'),
            (bool) $desde => 'Desde '.$desde->translatedFormat('d M Y'),
            (bool) $hasta => 'Hasta '.$hasta->translatedFormat('d M Y'),
            default => 'Todo el tiempo',
        };

        return view('admin.index', compact(
            'totalUsuarios',
            'usuariosActivos',
            'usuariosVerificados',
            'totalSugerencias',
            'sugerenciasPorEstado',
            'reportesPorTipo',
            'totalReportes',
            'crecimientoUsuarios',
            'usuariosMasActivos',
            'actividad',
            'variaciones',
            'etiquetaRango',
            'desde',
            'hasta',
        ));
    }

    private function aplicarRango(Builder $consulta, ?Carbon $desde, ?Carbon $hasta): Builder
    {
        return $consulta
            ->when($desde, fn (Builder $consulta) => $consulta->where('created_at', '>=', $desde))
            ->when($hasta, fn (Builder $consulta) => $consulta->where('created_at', '<=', $hasta));
    }

    private function contarEnRango(Builder $consulta, ?Carbon $desde, ?Carbon $hasta): int
    {
        return $this->aplicarRango($consulta, $desde, $hasta)->count();
    }

    /**
     * @return array{0: Carbon, 1: Carbon}|null
     */
    private function rangoAnterior(?Carbon $desde, ?Carbon $hasta): ?array
    {
        if (! $desde || ! $hasta) {
            return null;
        }

        $dias = (int) $desde->copy()->startOfDay()->diffInDays($hasta->copy()->startOfDay()) + 1;
        $hastaAnterior = $desde->copy()->subDay()->endOfDay();
        $desdeAnterior = $hastaAnterior->copy()->subDays($dias - 1)->startOfDay();

        return [$desdeAnterior, $hastaAnterior];
    }

    /**
     * @param  array{0: Carbon, 1: Carbon}|null  $rango
     */
    private function contarAnterior(Builder $consulta, ?array $rango): ?int
    {
        if (! $rango) {
            return null;
        }

        return $this->contarEnRango($consulta, $rango[0], $rango[1]);
    }

    /**
     * @param  array{0: Carbon, 1: Carbon}|null  $rango
     */
    private function contarReportesAnteriores(?array $rango): ?int
    {
        if (! $rango) {
            return null;
        }

        return $this->contarAnterior(UserReport::query(), $rango)
            + $this->contarAnterior(ProjectReport::query(), $rango)
            + $this->contarAnterior(DiaryReport::query(), $rango)
            + $this->contarAnterior(ProblemReport::query(), $rango);
    }

    private function variacion(int $actual, ?int $anterior): ?float
    {
        if ($anterior === null) {
            return null;
        }

        if ($anterior === 0) {
            return $actual > 0 ? 100.0 : 0.0;
        }

        return round((($actual - $anterior) / $anterior) * 100, 1);
    }
}
