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
    ];

    /**
     * @return BelongsTo
     */
    public function individual()
    {
        return $this->belongsTo(Individual::class, 'employee_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id', 'cod_escola');
    }

    /**
     * @return BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'cod_servidor');
    }

    /**
     * @return BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(ManagerRole::class, 'role_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function accessCriteria()
    {
        return $this->belongsTo(ManagerAccessCriteria::class, 'access_criteria_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function linkType()
    {
        return $this->belongsTo(ManagerLinkType::class, 'link_type_id', 'id');
    }

    /**
     * @return boolean
     */
    public function isChief()
    {
        return $this->chief;
    }

    /**
     * Filtra pelo ID da escola
     *
     * @param  Builder $query
     * @param  integer $schoolId
     * @return Builder
     */
    public function scopeOfSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }
}
