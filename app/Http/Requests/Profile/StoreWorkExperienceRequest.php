<?php

declare(strict_types=1);

namespace App\Http\Requests\Profile;

use App\Models\WorkExperience;
use Illuminate\Foundation\Http\FormRequest;

class StoreWorkExperienceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:'.implode(',', array_keys(WorkExperience::TYPES)),
            'employment_type' => 'nullable|in:'.implode(',', array_keys(WorkExperience::EMPLOYMENT_TYPES)),
            'company' => 'required|string|max:100',
            'position' => 'required|string|max:100',
            'location' => 'nullable|string|max:100',
            'work_mode' => 'required|in:'.implode(',', array_keys(WorkExperience::WORK_MODES)),
            'started_at' => 'required|date',
            'ended_at' => 'nullable|date|after_or_equal:started_at|required_if:current,0',
            'current' => 'nullable|in:on,1,0,true,false',
            'description' => 'nullable|string|max:1000',
            // Máximo 10 para que el CV y la tarjeta no se saturen.
            'technologies' => 'nullable|array|max:10',
            'technologies.*' => 'integer|distinct|exists:technologies,id',
        ];
    }
}
