<?php

declare(strict_types=1);

namespace App\Http\Requests\Project;

use App\Models\SkillEndorsement;
use Illuminate\Foundation\Http\FormRequest;

class StoreEndorsementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'skill_type' => 'required|string|in:' . implode(',', array_keys(SkillEndorsement::SKILLS)),
        ];
    }

    public function messages(): array
    {
        return [
            'skill_type.required' => 'Debes seleccionar una habilidad.',
            'skill_type.in' => 'La habilidad seleccionada no es válida.',
        ];
    }
}
