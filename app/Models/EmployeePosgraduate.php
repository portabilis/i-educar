<?php

namespace App\Models;

use iEducar\Modules\Educacenso\Model\AreaPosGraduacao;
use iEducar\Modules\Educacenso\Model\PosGraduacao;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeePosgraduate extends Model
{
    /**
     * @var string
     */
    protected $table = 'public.employee_posgraduate';

    /**
     * @var array
     */
    protected $fillable = [
        'employee_id',
        'entity_id',
        'type_id',
        'area_id',
        'completion_year',
    ];

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
