<?php

namespace App\Models;

use App\Models\Builders\EmployeeAllocationBuilder;
use App\Traits\HasLegacyDates;
use App\Traits\HasLegacyUserAction;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property LegacyPeriod $period
 * @property array<int, string> $fillable
 */
class EmployeeAllocation extends LegacyModel
{
    /** @use HasBuilder<EmployeeAllocationBuilder> */
    use HasBuilder;

    use HasLegacyDates;
    use HasLegacyUserAction;

    protected $primaryKey = 'cod_servidor_alocacao';

    protected $table = 'pmieducar.servidor_alocacao';

    protected static string $builder = EmployeeAllocationBuilder::class;

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
        'ativo',
        'ref_usuario_cad',
    ];

    protected function periodName(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->period->nome;
            },
        );
    }

    /**
     * @return BelongsTo<LegacyPeriod, $this>
     */
    public function period(): BelongsTo
    {
        return $this->belongsTo(LegacyPeriod::class, 'periodo');
    }

    /**
     * @return BelongsTo<LegacySchool, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(LegacySchool::class, 'ref_cod_escola', 'cod_escola');
    }

    /**
     * @return BelongsTo<LegacyEmployeeRole, $this>
     */
    public function employeeRole(): BelongsTo
    {
        return $this->belongsTo(LegacyEmployeeRole::class, 'ref_cod_servidor_funcao', 'cod_servidor_funcao');
    }

    /**
     * @return BelongsTo<Employee, $this>
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'ref_cod_servidor', 'cod_servidor');
    }

    /**
     * @return BelongsTo<LegacyBondType, $this>
     */
    public function bond(): BelongsTo
    {
        return $this->belongsTo(LegacyBondType::class, 'ref_cod_funcionario_vinculo', 'cod_funcionario_vinculo');
    }
}
