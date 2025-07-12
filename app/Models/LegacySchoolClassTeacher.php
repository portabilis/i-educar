<?php

namespace App\Models;

use iEducar\Modules\Servidores\Model\TipoVinculo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property array<int, string> $fillable
 * @property int $tipo_vinculo
 */
class LegacySchoolClassTeacher extends Model
{
    public const CREATED_AT = null;

    protected $fillable = [
        'ano',
        'instituicao_id',
        'turma_id',
        'servidor_id',
        'funcao_exercida',
        'tipo_vinculo',
        'data_inicial',
        'data_fim',
        'area_itinerario',
        'leciona_itinerario_tecnico_profissional',
    ];

    protected $table = 'modules.professor_turma';

    protected function linkTypeName(): Attribute
    {
        return Attribute::make(
            get: function () {
                return match ($this->tipo_vinculo) {
                    TipoVinculo::EFETIVO => 'Efetivo',
                    TipoVinculo::TEMPORARIO => 'Temporário',
                    TipoVinculo::TERCEIRIZADO => 'Terceirizado',
                    TipoVinculo::CLT => 'CLT',
                    default => null
                };
            },
        );
    }

    /**
     * @return HasMany<LegacySchoolClassTeacherDiscipline, $this>
     */
    public function schoolClassTeacherDisciplines(): HasMany
    {
        return $this->hasMany(LegacySchoolClassTeacherDiscipline::class, 'professor_turma_id');
    }

    /**
     * @return BelongsTo<LegacySchoolClass, $this>
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(LegacySchoolClass::class, 'turma_id');
    }

    /**
     * @return BelongsTo<LegacyPeriod, $this>
     */
    public function period(): BelongsTo
    {
        return $this->belongsTo(LegacyPeriod::class, 'turno_id');
    }

    /**
     * @return BelongsTo<Employee, $this>
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'servidor_id');
    }

    /**
     * @return BelongsToMany<LegacyDiscipline, $this>
     */
    public function disciplines(): BelongsToMany
    {
        return $this->belongsToMany(LegacyDiscipline::class, 'modules.professor_turma_disciplina', 'professor_turma_id', 'componente_curricular_id');
    }
}
