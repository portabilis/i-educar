<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeGraduation extends Model
{
    protected $fillable = [
        'employee_id',
        'course_id',
        'completion_year',
        'college_id',
        'discipline_id',
    ];

    /**
     * @return BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'cod_servidor');
    }

    /**
     * Filtra pelo ID do servidor
     *
     * @param Builder $query
     * @param         $employeeId
     *
     * @return Builder
     */
    public function scopeOfEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }
}
