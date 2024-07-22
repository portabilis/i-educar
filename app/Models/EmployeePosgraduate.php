<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property array<int, string> $fillable
 */
class EmployeePosgraduate extends Model
{
    protected $table = 'public.employee_posgraduate';

    protected $fillable = [
        'employee_id',
        'entity_id',
        'type_id',
        'area_id',
        'completion_year',
    ];

    /**
     * @return BelongsTo<Employee, $this>
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'cod_servidor');
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
