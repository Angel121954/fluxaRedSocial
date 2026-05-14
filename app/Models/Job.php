<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Job extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'company_name',
        'company_logo',
        'title',
        'description',
        'requirements',
        'responsibilities',
        'benefits',
        'location_type',
        'modality',
        'modality_label',
        'country',
        'city',
        'location',
        'seniority',
        'salary_min',
        'salary_max',
        'currency',
        'salary_currency',
        'is_featured',
        'tags',
        'application_url',
        'application_email',
        'expires_at',
        'status',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
        'tags' => 'array',
        'expires_at' => 'datetime',
    ];

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Technology::class, 'job_technology');
    }

    public function bookmarkedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'job_bookmarks')
            ->withTimestamps();
    }

    public function isSavedBy(?User $user): bool
    {
        if (!$user) return false;

        return $this->bookmarkedBy()
            ->where('user_id', $user->id)
            ->exists();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function getCompanyAttribute()
    {
        return $this->company_name;
    }
}
