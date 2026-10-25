<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Contact;
use App\Models\DiaryReport;
use App\Models\ProblemReport;
use App\Models\ProjectReport;
use App\Models\Suggestion;
use App\Models\User;
use App\Models\UserReport;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class MetricasDashboardService
{
    /**
     * @return array<string, mixed>
     */
    public function obtener(?Carbon $desde, ?Carbon $hasta): array
    {
        $rangoAnterior = $this->rangoAnterior($desde, $hasta);
        $usuarios = $this->metricasUsuarios($desde, $hasta, $rangoAnterior);
        $sugerencias = $this->metricasSugerencias($desde, $hasta, $rangoAnterior);
        $reportes = $this->metricasReportes($desde, $hasta, $rangoAnterior);

        return [
            'nuevosUsuarios' => $usuarios['total'],
            'nuevosActivos' => $usuarios['activos'],
            'nuevosVerificados' => $usuarios['verificados'],
            'totalSugerencias' => $sugerencias['total'],
            'sugerenciasPorEstado' => $sugerencias['porEstado'],
            'reportesPorTipo' => $reportes['porTipo'],
            'totalReportes' => $reportes['total'],
            'crecimientoUsuarios' => $this->crecimientoUsuarios($desde, $hasta),
            'usuariosMasActivos' => $this->usuariosMasActivos($desde, $hasta),
            'actividad' => $this->actividad($desde, $hasta),
            'variaciones' => [
                'nuevosUsuarios' => $this->variacion($usuarios['total'], $usuarios['totalAnterior']),
                'nuevosActivos' => $this->variacion($usuarios['activos'], $usuarios['activosAnterior']),
                'nuevosVerificados' => $this->variacion($usuarios['verificados'], $usuarios['verificadosAnterior']),
                'totalSugerencias' => $this->variacion($sugerencias['total'], $sugerencias['totalAnterior']),
                'totalReportes' => $this->variacion($reportes['total'], $reportes['totalAnterior']),
            ],
        ];
    }

    /**
     * @param  array{0: Carbon, 1: Carbon}|null  $rangoAnterior
     * @return array<string, int|null>
     */
    private function metricasUsuarios(?Carbon $desde, ?Carbon $hasta, ?array $rangoAnterior): array
    {
        if ($desde && $hasta && $rangoAnterior) {
            $fila = User::query()
                ->whereBetween('created_at', [$rangoAnterior[0], $hasta])
                ->selectRaw(
                    'SUM(created_at BETWEEN ? AND ?) as total,
                     SUM(created_at BETWEEN ? AND ? AND status = ?) as activos,
                     SUM(created_at BETWEEN ? AND ? AND email_verified_at IS NOT NULL) as verificados,
                     SUM(created_at BETWEEN ? AND ?) as total_anterior,
                     SUM(created_at BETWEEN ? AND ? AND status = ?) as activos_anterior,
                     SUM(created_at BETWEEN ? AND ? AND email_verified_at IS NOT NULL) as verificados_anterior',
                    [
                        $desde, $hasta,
                        $desde, $hasta, 'activo',
                        $desde, $hasta,
                        $rangoAnterior[0], $rangoAnterior[1],
                        $rangoAnterior[0], $rangoAnterior[1], 'activo',
                        $rangoAnterior[0], $rangoAnterior[1],
                    ]
                )
                ->first();

            return [
                'total' => (int) $fila->total,
                'activos' => (int) $fila->activos,
                'verificados' => (int) $fila->verificados,
                'totalAnterior' => (int) $fila->total_anterior,
                'activosAnterior' => (int) $fila->activos_anterior,
                'verificadosAnterior' => (int) $fila->verificados_anterior,
            ];
        }

        $fila = $this->aplicarRango(User::query(), $desde, $hasta)
            ->selectRaw('COUNT(*) as total, SUM(status = ?) as activos, SUM(email_verified_at IS NOT NULL) as verificados', ['activo'])
            ->first();

        return [
            'total' => (int) $fila->total,
            'activos' => (int) $fila->activos,
            'verificados' => (int) $fila->verificados,
            'totalAnterior' => null,
            'activosAnterior' => null,
            'verificadosAnterior' => null,
        ];
    }

    /**
     * @param  array{0: Carbon, 1: Carbon}|null  $rangoAnterior
     * @return array{total: int, totalAnterior: int|null, porEstado: Collection<string, int>}
     */
    private function metricasSugerencias(?Carbon $desde, ?Carbon $hasta, ?array $rangoAnterior): array
    {
        if ($desde && $hasta && $rangoAnterior) {
            $filas = Suggestion::query()
                ->whereBetween('created_at', [$rangoAnterior[0], $hasta])
                ->selectRaw(
                    'status, SUM(created_at BETWEEN ? AND ?) as actual, SUM(created_at BETWEEN ? AND ?) as anterior',
                    [$desde, $hasta, $rangoAnterior[0], $rangoAnterior[1]]
                )
                ->groupBy('status')
                ->get();

            return [
                'total' => (int) $filas->sum('actual'),
                'totalAnterior' => (int) $filas->sum('anterior'),
                'porEstado' => $filas->pluck('actual', 'status')->map(fn ($valor) => (int) $valor),
            ];
        }

        $filas = $this->aplicarRango(
            Suggestion::selectRaw('status, COUNT(*) as total')->groupBy('status'),
            $desde,
            $hasta,
        )->pluck('total', 'status');

        return [
            'total' => (int) $filas->sum(),
            'totalAnterior' => null,
            'porEstado' => $filas->map(fn ($valor) => (int) $valor),
        ];
    }

    /**
     * @param  array{0: Carbon, 1: Carbon}|null  $rangoAnterior
     * @return array{porTipo: Collection<string, int>, total: int, totalAnterior: int|null}
     */
    private function metricasReportes(?Carbon $desde, ?Carbon $hasta, ?array $rangoAnterior): array
    {
        $tipos = [
            'usuario' => UserReport::query(),
            'proyecto' => ProjectReport::query(),
            'diario' => DiaryReport::query(),
            'problema' => ProblemReport::query(),
        ];

        $porTipo = collect();
        $total = 0;
        $totalAnterior = 0;

        foreach ($tipos as $tipo => $consulta) {
            [$actual, $anterior] = $this->contarConAnterior($consulta, $desde, $hasta, $rangoAnterior);
            $porTipo[$tipo] = $actual;
            $total += $actual;
            $totalAnterior += $anterior ?? 0;
        }

        return [
            'porTipo' => $porTipo,
            'total' => $total,
            'totalAnterior' => $rangoAnterior ? $totalAnterior : null,
        ];
    }

    /**
     * @param  array{0: Carbon, 1: Carbon}|null  $rangoAnterior
     * @return array{0: int, 1: int|null}
     */
    private function contarConAnterior(Builder $consulta, ?Carbon $desde, ?Carbon $hasta, ?array $rangoAnterior): array
    {
        if (! $desde || ! $hasta || ! $rangoAnterior) {
            return [$this->contarEnRango($consulta, $desde, $hasta), null];
        }

        $fila = $consulta
            ->whereBetween('created_at', [$rangoAnterior[0], $hasta])
            ->selectRaw(
                'SUM(created_at BETWEEN ? AND ?) as actual, SUM(created_at BETWEEN ? AND ?) as anterior',
                [$desde, $hasta, $rangoAnterior[0], $rangoAnterior[1]]
            )
            ->first();

        return [(int) $fila->actual, (int) $fila->anterior];
    }

    private function crecimientoUsuarios(?Carbon $desde, ?Carbon $hasta): Collection
    {
        return $this->aplicarRango(
            User::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total")
                ->groupBy('month')
                ->orderBy('month'),
            $desde,
            $hasta,
        )->get();
    }

    private function usuariosMasActivos(?Carbon $desde, ?Carbon $hasta): Collection
    {
        return User::with('profile')
            ->withCount(['projects' => fn (Builder $consulta) => $this->aplicarRango($consulta, $desde, $hasta)])
            ->orderByDesc('projects_count')
            ->take(5)
            ->get();
    }

    private function actividad(?Carbon $desde, ?Carbon $hasta): Collection
    {
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

        return collect()
            ->merge($usuariosRecientes)
            ->merge($sugerenciasRecientes)
            ->merge($contactosRecientes)
            ->sortByDesc('hora')
            ->take(5)
            ->values();
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
