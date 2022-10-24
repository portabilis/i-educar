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
        'chief'
    ];

    /**
     * @return BelongsTo
     */
    public function individual(): BelongsTo
    {
        return $this->belongsTo(Individual::class, 'employee_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(LegacySchool::class, 'school_id', 'cod_escola');
    }

    /**
     * @return BelongsTo
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'cod_servidor');
    }

    /**
     * @return BelongsTo
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(ManagerRole::class, 'role_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function accessCriteria(): BelongsTo
    {
        return $this->belongsTo(ManagerAccessCriteria::class, 'access_criteria_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function linkType(): BelongsTo
    {
        return $this->belongsTo(ManagerLinkType::class, 'link_type_id', 'id');
    }

    /**
     * @return boolean
     */
    public function isChief(): bool
    {
        return $this->chief;
    }

    /**
     * Filtra pelo ID da escola
     *
     * @param Builder $query
     * @param integer $schoolId
     *
     * @return Builder
     */
    public function scopeOfSchool(Builder $query, int $schoolId): Builder
    {
        return $query->where('school_id', $schoolId);
    }
}
