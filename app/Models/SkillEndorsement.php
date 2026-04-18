<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkillEndorsement extends Model
{
    protected $fillable = [
        'user_id',
        'project_id',
        'skill_type',
    ];

    public const SKILLS = [
        'technical_communication' => [
            'label' => 'Comunicación Técnica',
            'icon' => 'chat',
            'color' => '#3B82F6',
        ],
        'logical_thinking' => [
            'label' => 'Pensamiento Lógico',
            'icon' => 'branch',
            'color' => '#8B5CF6',
        ],
        'collaboration' => [
            'label' => 'Colaboración',
            'icon' => 'nodes',
            'color' => '#10B981',
        ],
        'architecture' => [
            'label' => 'Arquitectura',
            'icon' => 'layers',
            'color' => '#F59E0B',
        ],
        'leadership' => [
            'label' => 'Liderazgo',
            'icon' => 'compass',
            'color' => '#EF4444',
        ],
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public static function getSkillCounts(int $projectId): array
    {
        $counts = self::where('project_id', $projectId)
            ->select('skill_type')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('skill_type')
            ->pluck('count', 'skill_type')
            ->toArray();

        foreach (self::SKILLS as $key => $skill) {
            if (!isset($counts[$key])) {
                $counts[$key] = 0;
            }
        }

        return $counts;
    }

    public static function getUserEndorsement(int $userId, int $projectId): ?string
    {
        $endorsement = self::where('user_id', $userId)
            ->where('project_id', $projectId)
            ->first();

        return $endorsement?->skill_type;
    }
}