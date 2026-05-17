<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\User;
use App\Notifications\CreatesNotifications;
use Illuminate\Support\Facades\DB;

class BadgeService
{
    use CreatesNotifications;

    public function scanUser(User $user): void
    {
        $badges = Badge::where('criteria_type', 'count_check')->get();

        foreach ($badges as $badge) {
            if ($user->badges()->where('badge_id', $badge->id)->exists()) {
                continue;
            }

            if ($this->userMeetsCriteria($user, $badge)) {
                $this->awardBadge($user, $badge);
            }
        }
    }

    public function scanAll(): int
    {
        $awarded = 0;

        User::chunk(100, function ($users) use (&$awarded) {
            foreach ($users as $user) {
                $badges = Badge::where('criteria_type', 'count_check')
                    ->whereNotIn('id', $user->badges()->pluck('badge_id'))
                    ->get();

                foreach ($badges as $badge) {
                    if ($this->userMeetsCriteria($user, $badge)) {
                        $this->awardBadge($user, $badge);
                        $awarded++;
                    }
                }
            }
        });

        return $awarded;
    }

    public function userMeetsCriteria(User $user, Badge $badge): bool
    {
        $config = $badge->criteria_config;
        $model = $config['model'] ?? null;
        $operator = $config['operator'] ?? '>=';
        $value = $config['value'] ?? 1;
        $relation = $config['relation'] ?? null;

        $count = match ($model) {
            'projects' => $user->projects()->count(),
            'comments' => $user->comments()->count(),
            'followers' => $user->followers()->count(),
            'work_experiences' => $user->workExperiences()->count(),
            'educations' => $user->educations()->count(),
            'technologies' => $user->technologies()->count(),
            'salary_reports' => $user->salaryReports()->count(),
            'profile_completeness' => $this->getProfileCompleteness($user),
            'account_age_days' => $user->created_at->diffInDays(now()),
            default => 0,
        };

        return match ($operator) {
            '>=' => $count >= $value,
            '>' => $count > $value,
            '==' => $count == $value,
            '<=' => $count <= $value,
            default => false,
        };
    }

    public function awardBadge(User $user, Badge $badge): void
    {
        DB::transaction(function () use ($user, $badge) {
            $user->badges()->attach($badge->id, [
                'earned_at' => now(),
                'notified' => false,
            ]);

            $this->notifyBadgeEarned($user, $badge);
        });
    }

    private function notifyBadgeEarned(User $user, Badge $badge): void
    {
        static::createNotification(
            userId: $user->id,
            type: 'badge',
            title: '¡Nuevo logro!',
            body: "Has obtenido la insignia «{$badge->name}» — {$badge->description}",
            link: route('profile.index', ['tab' => 'badges']),
            broadcast: true,
        );
    }

    private function getProfileCompleteness(User $user): int
    {
        $profile = $user->profile;
        if (! $profile) {
            return 0;
        }

        $score = 0;

        if ($profile->bio) {
            $score += 20;
        }
        if ($profile->avatar && ! str_contains($profile->avatar, 'dicebear')) {
            $score += 20;
        }
        if ($profile->country) {
            $score += 15;
        }
        if ($profile->github_url || $profile->linkedin_url || $profile->twitter_url) {
            $score += 15;
        }
        if ($user->technologies()->count() > 0) {
            $score += 15;
        }
        if ($user->workExperiences()->count() > 0) {
            $score += 15;
        }

        return $score;
    }
}
