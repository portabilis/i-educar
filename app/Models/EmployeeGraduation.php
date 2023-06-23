<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeGraduation extends LegacyModel
{
    protected $fillable = [
        'employee_id',
        'course_id',
        'completion_year',
        'college_id',
        'discipline_id',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function educacensoDegree(): BelongsTo
    {
        return $this->belongsTo(EducacensoDegree::class, 'course_id');
    }

    public function educacensoInstitution(): BelongsTo
    {
        return $this->belongsTo(EducacensoInstitution::class, 'college_id');
    }

    /**
     * Filtra pelo ID do servidor
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeOfEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }
}
