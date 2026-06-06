<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryReport extends Model
{
    protected $fillable = [
        'user_id',
        'country',
        'city',
        'seniority',
        'experience_years',
        'salary_usd',
        'currency',
        'modality',
        'company',
        'verified',
    ];

    protected function casts(): array
    {
        return [
            'verified' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function technologies()
    {
        return $this->belongsToMany(Technology::class, 'salary_report_technology');
    }
}
