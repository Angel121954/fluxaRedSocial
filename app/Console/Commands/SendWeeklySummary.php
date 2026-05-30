<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Mail\WeeklySummary;
use App\Models\Project;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendWeeklySummary extends Command
{
    protected $signature = 'notifications:weekly-summary {--user= : ID de usuario específico para enviar el resumen}';

    protected $description = 'Envía el resumen semanal por email a los usuarios que lo tienen activado';

    public function handle(): void
    {
        $userId = $this->option('user');

        if ($userId) {
            $user = User::with('notificationPreferences')->find($userId);

            if (! $user) {
                $this->error("Usuario #{$userId} no encontrado.");

                return;
            }

            if (! $user->notificationPreferences?->weekly_summary || ! $user->notificationPreferences?->email_enabled) {
                $this->warn("El usuario #{$userId} no tiene activado el resumen semanal o el email.");

                return;
            }

            $trending = $this->getTrendingProjects();

            $this->sendSummary($user, $trending);

            return;
        }

        $users = User::whereHas('notificationPreferences', function ($q) {
            $q->where('weekly_summary', true)
                ->where('email_enabled', true);
        })->with('notificationPreferences')->get();

        if ($users->isEmpty()) {
            $this->info('No hay usuarios con resumen semanal activado.');

            return;
        }

        $trending = $this->getTrendingProjects();
        $sent = 0;
        $errors = 0;

        foreach ($users as $user) {
            if ($this->sendSummary($user, $trending)) {
                $sent++;
            } else {
                $errors++;
            }
        }

        $this->info("Resumen semanal enviado a {$sent} usuarios. Errores: {$errors}.");
    }

    private function sendSummary(User $user, $trending): bool
    {
        if (! $user->email || ! $user->hasVerifiedEmail()) {
            return false;
        }

        try {
            $stats = [
                'badges_earned'          => $this->countBadgesEarned($user),
                'projects_created'       => $this->countProjectsCreated($user),
                'diary_responses'        => $this->countDiaryResponses($user),
                'comments_made'          => $this->countCommentsMade($user),
                'work_experiences_added' => $this->countWorkExperiencesAdded($user),
                'educations_added'       => $this->countEducationsAdded($user),
                'salary_reports'         => $this->countSalaryReports($user),
                'endorsements_received'  => $this->countEndorsementsReceived($user),
                'new_followers'          => $this->countNewFollowers($user),
                'new_likes'              => $this->countNewLikes($user),
                'new_comments'           => $this->countNewComments($user),
            ];

            Mail::to($user)->send(new WeeklySummary($user, $stats, $trending));

            return true;
        } catch (\Throwable $e) {
            Log::error('Error enviando resumen semanal', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);

            $this->error("Error con usuario #{$user->id}: {$e->getMessage()}");

            return false;
        }
    }

    private function getTrendingProjects()
    {
        return Project::with('user')
            ->where('privacy', 'public')
            ->where('created_at', '>=', now()->subWeek())
            ->orderByDesc('likes_count')
            ->orderByDesc('comments_count')
            ->take(5)
            ->get();
    }

    private function countBadgesEarned(User $user): int
    {
        return $user->badges()
            ->wherePivot('earned_at', '>=', now()->subWeek())
            ->count();
    }

    private function countProjectsCreated(User $user): int
    {
        return $user->projects()
            ->where('created_at', '>=', now()->subWeek())
            ->count();
    }

    private function countDiaryResponses(User $user): int
    {
        return DB::table('diary_responses')
            ->where('user_id', $user->id)
            ->where('created_at', '>=', now()->subWeek())
            ->count();
    }

    private function countCommentsMade(User $user): int
    {
        return $user->comments()
            ->where('created_at', '>=', now()->subWeek())
            ->count();
    }

    private function countWorkExperiencesAdded(User $user): int
    {
        return $user->workExperiences()
            ->where('created_at', '>=', now()->subWeek())
            ->count();
    }

    private function countEducationsAdded(User $user): int
    {
        return $user->educations()
            ->where('created_at', '>=', now()->subWeek())
            ->count();
    }

    private function countSalaryReports(User $user): int
    {
        return $user->salaryReports()
            ->where('created_at', '>=', now()->subWeek())
            ->count();
    }

    private function countEndorsementsReceived(User $user): int
    {
        return DB::table('skill_endorsements')
            ->where('user_id', $user->id)
            ->where('created_at', '>=', now()->subWeek())
            ->count();
    }

    private function countNewFollowers(User $user): int
    {
        return DB::table('follows')
            ->where('followed_id', $user->id)
            ->where('created_at', '>=', now()->subWeek())
            ->count();
    }

    private function countNewLikes(User $user): int
    {
        $projectIds = $user->projects()->pluck('id');

        if ($projectIds->isEmpty()) {
            return 0;
        }

        return DB::table('project_likes')
            ->whereIn('project_id', $projectIds)
            ->where('created_at', '>=', now()->subWeek())
            ->count();
    }

    private function countNewComments(User $user): int
    {
        $projectIds = $user->projects()->pluck('id');

        if ($projectIds->isEmpty()) {
            return 0;
        }

        return DB::table('comments')
            ->whereIn('project_id', $projectIds)
            ->where('created_at', '>=', now()->subWeek())
            ->count();
    }
}
