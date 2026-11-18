<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AnalyticsEvent;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

final class FunnelMetricsService
{
    private const ACTION_EVENTS = ['project_published', 'comment_created'];

    private const BUILDER_EVENTS = ['project_published', 'comment_created', 'follow_created', 'project_shared'];

    /**
     * @return array<string, mixed>
     */
    public function build(Carbon $from, Carbon $to): array
    {
        $funnel = $this->funnel($from, $to);
        $summary = $this->summary($funnel);
        $summary['page_views'] = $this->countEvents('page_viewed', $from, $to);

        return [
            'funnel' => $funnel,
            'summary' => $summary,
            'sources' => $this->sources($from, $to),
            'retention' => $this->retention($from, $to),
            'activity' => $this->activity($from, $to),
        ];
    }

    /**
     * @return array<int, array{key: string, label: string, count: int, rate: float, overall: float}>
     */
    public function funnel(Carbon $from, Carbon $to): array
    {
        $steps = [
            ['key' => 'page_viewed', 'label' => 'Visitas públicas', 'count' => $this->countEvents('page_viewed', $from, $to)],
            ['key' => 'user_registered', 'label' => 'Se registraron', 'count' => $this->countUsers('user_registered', $from, $to)],
            ['key' => 'email_verified', 'label' => 'Verificaron el email', 'count' => $this->countUsers('email_verified', $from, $to)],
            ['key' => 'onboarding_completed', 'label' => 'Completaron el onboarding', 'count' => $this->countUsers('onboarding_completed', $from, $to)],
            ['key' => 'activated', 'label' => 'Se activaron (7 días)', 'count' => $this->activatedUsers($from, $to)],
        ];

        // Si aún no hay visitas registradas, el embudo arranca en el primer paso con datos.
        while (count($steps) > 1 && $steps[0]['count'] === 0) {
            array_shift($steps);
        }

        $firstCount = $steps[0]['count'];
        $previousCount = null;
        $funnel = [];

        foreach ($steps as $step) {
            $rate = match (true) {
                $previousCount === null => $step['count'] > 0 ? 100.0 : 0.0,
                $previousCount === 0 => 0.0,
                default => round(($step['count'] / $previousCount) * 100, 1),
            };

            $overall = $firstCount === 0
                ? 0.0
                : round(($step['count'] / $firstCount) * 100, 1);

            $funnel[] = [
                ...$step,
                'rate' => $rate,
                'overall' => $overall,
            ];

            $previousCount = $step['count'];
        }

        return $funnel;
    }

    /**
     * @param  array<int, array{key: string, label: string, count: int, rate: float, overall: float}>  $funnel
     * @return array<string, int|float>
     */
    private function summary(array $funnel): array
    {
        $byKey = collect($funnel)->keyBy('key');
        $registrations = (int) ($byKey['user_registered']['count'] ?? 0);
        $activated = (int) ($byKey['activated']['count'] ?? 0);

        return [
            'registrations' => $registrations,
            'verified' => (int) ($byKey['email_verified']['count'] ?? 0),
            'onboarding' => (int) ($byKey['onboarding_completed']['count'] ?? 0),
            'activated' => $activated,
            'activation_rate' => $registrations > 0 ? round(($activated / $registrations) * 100, 1) : 0.0,
            'weekly_active_builders' => $this->weeklyActiveBuilders(),
        ];
    }

    public function weeklyActiveBuilders(): int
    {
        return AnalyticsEvent::query()
            ->whereIn('name', self::BUILDER_EVENTS)
            ->where('created_at', '>=', now()->subDays(7))
            ->whereNotNull('user_id')
            ->distinct()
            ->count('user_id');
    }

    /**
     * @return Collection<int, array{source: string, visits: int, registrations: int, activated: int, activation_rate: float}>
     */
    public function sources(Carbon $from, Carbon $to): Collection
    {
        $registrations = AnalyticsEvent::query()
            ->where('name', 'user_registered')
            ->whereBetween('created_at', [$from, $to])
            ->whereNotNull('user_id')
            ->get(['user_id', 'source', 'created_at'])
            ->unique('user_id')
            ->values();

        if ($registrations->isEmpty()) {
            return collect();
        }

        $actions = AnalyticsEvent::query()
            ->whereIn('name', self::ACTION_EVENTS)
            ->whereIn('user_id', $registrations->pluck('user_id'))
            ->whereBetween('created_at', [$from, $to->copy()->addDays(7)])
            ->get(['user_id', 'created_at'])
            ->groupBy('user_id');

        $visits = AnalyticsEvent::query()
            ->where('name', 'page_viewed')
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw("COALESCE(NULLIF(source, ''), 'directo') as source, COUNT(*) as total")
            ->groupBy('source')
            ->pluck('total', 'source');

        return $registrations
            ->groupBy(fn (AnalyticsEvent $registration): string => $registration->source ?: 'directo')
            ->map(function (Collection $group, string $source) use ($actions, $visits): array {
                $activated = $group->filter(function (AnalyticsEvent $registration) use ($actions): bool {
                    $userActions = $actions->get($registration->user_id, collect());

                    return $userActions->contains(function (AnalyticsEvent $action) use ($registration): bool {
                        return $action->created_at->between(
                            $registration->created_at,
                            $registration->created_at->copy()->addDays(7),
                        );
                    });
                })->count();

                return [
                    'source' => $source,
                    'visits' => (int) ($visits[$source] ?? 0),
                    'registrations' => $group->count(),
                    'activated' => $activated,
                    'activation_rate' => $group->count() > 0
                        ? round(($activated / $group->count()) * 100, 1)
                        : 0.0,
                ];
            })
            ->sortByDesc('registrations')
            ->values();
    }

    /**
     * @return array{total: int, d1: int, d7: int, d30: int, d1_rate: float, d7_rate: float, d30_rate: float}
     */
    public function retention(Carbon $from, Carbon $to): array
    {
        $registrations = AnalyticsEvent::query()
            ->where('name', 'user_registered')
            ->whereBetween('created_at', [$from, $to])
            ->whereNotNull('user_id')
            ->get(['user_id', 'created_at'])
            ->unique('user_id')
            ->values();

        if ($registrations->isEmpty()) {
            return [
                'total' => 0,
                'd1' => 0,
                'd7' => 0,
                'd30' => 0,
                'd1_rate' => 0.0,
                'd7_rate' => 0.0,
                'd30_rate' => 0.0,
            ];
        }

        $sessions = AnalyticsEvent::query()
            ->where('name', 'session_started')
            ->whereIn('user_id', $registrations->pluck('user_id'))
            ->whereBetween('created_at', [$from, $to->copy()->addDays(31)])
            ->get(['user_id', 'created_at'])
            ->groupBy('user_id');

        $d1 = 0;
        $d7 = 0;
        $d30 = 0;

        foreach ($registrations as $registration) {
            $userSessions = $sessions->get($registration->user_id, collect());

            if ($this->hasSessionOnDay($userSessions, $registration->created_at, 1)) {
                $d1++;
            }
            if ($this->hasSessionOnDay($userSessions, $registration->created_at, 7)) {
                $d7++;
            }
            if ($this->hasSessionOnDay($userSessions, $registration->created_at, 30)) {
                $d30++;
            }
        }

        $total = $registrations->count();

        return [
            'total' => $total,
            'd1' => $d1,
            'd7' => $d7,
            'd30' => $d30,
            'd1_rate' => round(($d1 / $total) * 100, 1),
            'd7_rate' => round(($d7 / $total) * 100, 1),
            'd30_rate' => round(($d30 / $total) * 100, 1),
        ];
    }

    /**
     * @return array{labels: array<int, string>, registrations: array<int, int>, actions: array<int, int>}
     */
    public function activity(Carbon $from, Carbon $to): array
    {
        $groupByMonth = (int) $from->diffInDays($to) > 120;

        $rows = AnalyticsEvent::query()
            ->whereIn('name', ['user_registered', ...self::ACTION_EVENTS])
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw(
                $groupByMonth
                    ? "DATE_FORMAT(created_at, '%Y-%m') as period, name, COUNT(*) as total"
                    : 'DATE(created_at) as period, name, COUNT(*) as total',
            )
            ->groupBy('period', 'name')
            ->get()
            ->groupBy('period');

        $labels = [];
        $registrations = [];
        $actions = [];

        if ($groupByMonth) {
            $periods = CarbonPeriod::create($from->copy()->startOfMonth(), '1 month', $to->copy()->startOfMonth());
            $format = 'Y-m';
        } else {
            $periods = CarbonPeriod::create($from->copy()->startOfDay(), '1 day', $to->copy()->startOfDay());
            $format = 'Y-m-d';
        }

        foreach ($periods as $period) {
            $key = $period->format($format);
            $periodRows = $rows->get($key, collect());

            $labels[] = $groupByMonth
                ? $period->translatedFormat('M Y')
                : $period->format('d M');

            $registrations[] = (int) $periodRows->where('name', 'user_registered')->sum('total');
            $actions[] = (int) $periodRows
                ->whereIn('name', self::ACTION_EVENTS)
                ->sum('total');
        }

        return [
            'labels' => $labels,
            'registrations' => $registrations,
            'actions' => $actions,
        ];
    }

    public function activatedUsers(Carbon $from, Carbon $to): int
    {
        return AnalyticsEvent::query()
            ->where('name', 'user_registered')
            ->whereBetween('created_at', [$from, $to])
            ->whereNotNull('user_id')
            ->whereExists(function ($query): void {
                $query->selectRaw('1')
                    ->from('analytics_events as actions')
                    ->whereColumn('actions.user_id', 'analytics_events.user_id')
                    ->whereIn('actions.name', self::ACTION_EVENTS)
                    ->whereRaw('actions.created_at >= analytics_events.created_at')
                    ->whereRaw('actions.created_at <= DATE_ADD(analytics_events.created_at, INTERVAL 7 DAY)');
            })
            ->distinct()
            ->count('user_id');
    }

    private function countEvents(string $name, Carbon $from, Carbon $to): int
    {
        return AnalyticsEvent::query()
            ->where('name', $name)
            ->whereBetween('created_at', [$from, $to])
            ->count();
    }

    private function countUsers(string $name, Carbon $from, Carbon $to): int
    {
        return AnalyticsEvent::query()
            ->where('name', $name)
            ->whereBetween('created_at', [$from, $to])
            ->whereNotNull('user_id')
            ->distinct()
            ->count('user_id');
    }

    private function hasSessionOnDay(Collection $sessions, Carbon $registeredAt, int $day): bool
    {
        $start = $registeredAt->copy()->addDays($day);
        $end = $start->copy()->addDay();

        return $sessions->contains(
            fn (AnalyticsEvent $session): bool => $session->created_at->greaterThanOrEqualTo($start)
                && $session->created_at->lessThan($end),
        );
    }
}
