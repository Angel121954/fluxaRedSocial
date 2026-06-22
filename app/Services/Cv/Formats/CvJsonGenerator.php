<?php

declare(strict_types=1);

namespace App\Services\Cv\Formats;

use App\Models\User;
use App\Models\WorkExperience;

class CvJsonGenerator
{
    public function generate(User $user, array $data): array
    {
        $profile = $data['profile'];
        $cvSettings = $data['cvSettings'];

        $json = [
            'meta' => [
                'generator' => 'Fluxa',
                'version' => '1.0',
                'generated_at' => now()->toIso8601String(),
                'profile_url' => 'https://'.($data['urlPerfil'] ?? request()->getHost().'/'.$user->username),
            ],
            'personal' => [
                'name' => $user->name,
                'username' => $user->username,
                'email' => ($cvSettings['show_email'] ?? true) ? $user->email : null,
                'role' => $data['rolProfesional'] ?? null,
                'bio' => $profile->bio ?? null,
                'location' => ($cvSettings['show_location'] ?? true)
                    ? trim(($profile->city ?? '').', '.($profile->country ?? ''), ' ,')
                    : null,
                'phone' => $profile->phone_number
                    ? ($profile->phone_code ?? '').' '.$profile->phone_number
                    : null,
                'website' => $profile->website_url ?? null,
            ],
            'social' => array_filter([
                'linkedin' => $profile->linkedin_url ?? null,
                'twitter' => $profile->twitter_url ?? null,
                'github' => $profile->github_url ?? null,
            ]),
            'sections' => [],
        ];

        $sectionRenderers = [
            'skills' => fn () => [
                'title' => 'Habilidades Técnicas',
                'type' => 'skills',
                'items' => $data['technologies']->pluck('name')->toArray(),
            ],
            'experience' => fn () => [
                'title' => 'Experiencia Laboral',
                'type' => 'experience',
                'items' => $data['workExperiences']->map(fn ($exp) => [
                    'position' => $exp->position,
                    'company' => $exp->company,
                    'location' => $exp->location,
                    'type' => WorkExperience::TYPES[$exp->type] ?? $exp->type,
                    'start_date' => $exp->started_at?->format('Y-m-d'),
                    'end_date' => $exp->ended_at?->format('Y-m-d'),
                    'current' => (bool) $exp->current,
                    'description' => $exp->description,
                ])->toArray(),
            ],
            'education' => fn () => [
                'title' => 'Educación',
                'type' => 'education',
                'items' => $data['educations']->map(fn ($edu) => [
                    'degree' => $edu->degree,
                    'institution' => $edu->institution,
                    'field' => $edu->field,
                    'graduated_year' => $edu->graduated_year,
                    'current' => (bool) $edu->current,
                ])->toArray(),
            ],
            'projects' => fn () => [
                'title' => 'Proyectos',
                'type' => 'projects',
                'items' => $data['projects']->map(fn ($proj) => [
                    'title' => $proj->title,
                    'description' => $proj->content,
                    'url' => $proj->slug ? route('projects.show', $proj->slug) : null,
                    'technologies' => $proj->technologies->pluck('name')->toArray(),
                ])->toArray(),
            ],
        ];

        $settingsVisibility = [
            'skills' => true,
            'experience' => $cvSettings['show_experience'] ?? true,
            'education' => $cvSettings['show_education'] ?? true,
            'projects' => $cvSettings['show_projects'] ?? true,
        ];

        foreach ($cvSettings['section_order'] as $section) {
            if (! isset($sectionRenderers[$section]) || ! ($settingsVisibility[$section] ?? true)) {
                continue;
            }

            $sectionData = $sectionRenderers[$section]();

            if (empty($sectionData['items'])) {
                continue;
            }

            $json['sections'][] = $sectionData;
        }

        return $json;
    }

    public function generateJsonString(User $user, array $data): string
    {
        return json_encode(
            $this->generate($user, $data),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }
}
