<?php

namespace App\Models;

use App\Models\Builders\EmployeePosgraduateBuilder;
use iEducar\Modules\Educacenso\Model\AreaPosGraduacao;
use iEducar\Modules\Educacenso\Model\PosGraduacao;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property array<int, string> $fillable
 * @property int $type_id
 * @property int $area_id
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

    protected function typeName(): Attribute
    {
        return Attribute::make(
            get: fn () => PosGraduacao::getDescriptiveValues()[$this->type_id] ?? null
        );
    }

    protected function areaName(): Attribute
    {
        return Attribute::make(
            get: fn () => AreaPosGraduacao::getDescriptiveValues()[$this->area_id] ?? null
        );
    }
}
