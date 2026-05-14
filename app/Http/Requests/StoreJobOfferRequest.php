<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null
            && $this->user()->account_type === 'company';
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:120',
            'description' => 'required|string|max:2000',
            'modality' => 'required|in:remoto,hibrido,presencial',
            'seniority' => 'required|in:junior,mid,senior,lead',
            'location' => 'nullable|string|max:100',
            'salary_min' => 'nullable|integer|min:0|max:999999',
            'salary_max' => 'nullable|integer|min:0|max:999999',
            'currency' => 'nullable|string|size:3',
            'whatsapp' => 'nullable|string|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'El título del cargo es obligatorio.',
            'description.required' => 'La descripción es obligatoria.',
            'modality.required' => 'Selecciona una modalidad.',
            'seniority.required' => 'Selecciona un nivel de seniority.',
        ];
    }
}
