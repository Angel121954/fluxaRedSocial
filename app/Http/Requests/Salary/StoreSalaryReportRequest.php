<?php

declare(strict_types=1);

namespace App\Http\Requests\Salary;

use Illuminate\Foundation\Http\FormRequest;

class StoreSalaryReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->role !== 'guest';
    }

    public function rules(): array
    {
        return [
            'country' => 'required|string|max:100',
            'city' => 'nullable|string|max:100',
            'seniority' => 'required|in:junior,mid,senior,lead',
            'experience_years' => 'required|integer|min:0|max:70',
            'salary_usd' => 'required|integer|min:1000|max:999999',
            'modality' => 'required|in:remote,hybrid,onsite',
            'company' => 'nullable|string|max:150',
            'technologies' => 'nullable|array',
            'technologies.*' => 'exists:technologies,id',
        ];
    }
}
