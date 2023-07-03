<?php

namespace App\Models;

use App\Models\Builders\EmployeeAllocationBuilder;
use App\Traits\HasLegacyDates;
use App\Traits\HasLegacyUserAction;
use Illuminate\Database\Eloquent\Casts\Attribute;

class EmployeeAllocation extends LegacyModel
{
    use HasLegacyUserAction;
    use HasLegacyDates;

    protected $primaryKey = 'cod_servidor_alocacao';

    protected $table = 'pmieducar.servidor_alocacao';

    public string $builder = EmployeeAllocationBuilder::class;

    protected $fillable = [
        'carga_horaria',
        'periodo',
        'hora_final',
        'hora_inicial',
        'dia_semana',
        'ano',
        'data_admissao',
        'ref_ref_cod_instituicao',
        'ref_cod_escola',
        'ref_cod_servidor',
        'ref_cod_servidor_funcao',
        'ref_cod_funcionario_vinculo',
        'hora_atividade',
        'horas_excedentes',
        'data_saida',
    ];

    protected function periodName(): Attribute
    {
        return Attribute::make(
            get: function () {
                return match ($this->periodo) {
                    1 => 'Matutino',
                    2 => 'Vespertino',
                    3 => 'Noturno',
                    4 => 'Integral',
                    default => null
                };
            },
        );
    }

    public function school()
    {
        return $this->belongsTo(LegacySchool::class, 'ref_cod_escola', 'cod_escola');
    }

    public function employeeRole()
    {
        return $this->belongsTo(LegacyEmployeeRole::class, 'ref_cod_servidor_funcao', 'cod_servidor_funcao');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'ref_cod_servidor', 'cod_servidor');
    }
}
