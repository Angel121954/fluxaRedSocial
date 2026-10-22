<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class WorkExperience extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'employment_type',
        'company',
        'position',
        'location',
        'work_mode',
        'started_at',
        'ended_at',
        'current',
        'description',
    ];

    protected $casts = [
        'started_at' => 'date:Y-m-d',
        'ended_at' => 'date:Y-m-d',
        'current' => 'boolean',
    ];

    // Qué clase de experiencia es (define el contexto del registro).
    public const TYPES = [
        'formal' => 'Empleo formal',
        'practicum' => 'Práctica / Etapa productiva',
        'freelance' => 'Freelance / Cliente',
        'volunteering' => 'Voluntariado',
    ];

    // Bajo qué modalidad contractual se dio la experiencia.
    public const EMPLOYMENT_TYPES = [
        'full_time' => 'Tiempo completo',
        'part_time' => 'Medio tiempo',
        'contract' => 'Contrato / Prestación de servicios',
        'internship' => 'Pasantía / Aprendizaje',
        'temporary' => 'Temporal',
    ];

    // Dónde se ejecutó el trabajo. Antes vivía como texto libre dentro de location.
    public const WORK_MODES = [
        'onsite' => 'Presencial',
        'remote' => 'Remoto',
        'hybrid' => 'Híbrido',
    ];

    // Relación con User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Tecnologías usadas en esta experiencia (pivot technology_work_experience).
    public function technologies(): BelongsToMany
    {
        return $this->belongsToMany(Technology::class, 'technology_work_experience');
    }
}
