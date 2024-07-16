<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeGraduation extends LegacyModel
{
    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'course_id',
        'completion_year',
        'college_id',
        'discipline_id',
    ];

    /**
     * @return BelongsTo<Employee, $this>
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * @return BelongsTo<EducacensoDegree, $this>
     */
    public function educacensoDegree(): BelongsTo
    {
        return $this->belongsTo(EducacensoDegree::class, 'course_id');
    }

    /**
     * @return BelongsTo<EducacensoInstitution, $this>
     */
    public function educacensoInstitution(): BelongsTo
    {
        return $this->belongsTo(EducacensoInstitution::class, 'college_id');
    }

    /**
     * Filtra pelo ID do servidor
     *
     * @phpstan-ignore-next-line
     */
    public function scopeOfEmployee(Builder $query, $employeeId): Builder
    {
        return $query->where('employee_id', $employeeId);
    }
}
