<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolManager extends Model
{
    protected $fillable = [
        'employee_id',
        'school_id',
        'role_id',
        'access_criteria_id',
        'access_criteria_description',
        'link_type_id',
        'chief',
    ];

    public function individual(): BelongsTo
    {
        return $this->belongsTo(Individual::class, 'employee_id', 'id');
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(LegacySchool::class, 'school_id', 'cod_escola');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'cod_servidor');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(ManagerRole::class, 'role_id', 'id');
    }

    public function accessCriteria(): BelongsTo
    {
        return $this->belongsTo(ManagerAccessCriteria::class, 'access_criteria_id', 'id');
    }

    public function linkType(): BelongsTo
    {
        return $this->belongsTo(ManagerLinkType::class, 'link_type_id', 'id');
    }

    public function isChief(): bool
    {
        return (bool) $this->chief;
    }

    /**
     * Filtra pelo ID da escola
     */
    public function scopeOfSchool(Builder $query, int $schoolId): Builder
    {
        return $query->where('school_id', $schoolId);
    }
}
