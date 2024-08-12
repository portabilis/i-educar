<?php

namespace App\Models;

use App\Models\Builders\EmployeePosgraduateBuilder;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property array<int, string> $fillable
 */
class EmployeePosgraduate extends Model
{
    /** @use HasBuilder<EmployeePosgraduateBuilder> */
    use HasBuilder;

    protected $table = 'public.employee_posgraduate';

    protected static string $builder = EmployeePosgraduateBuilder::class;

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
}
