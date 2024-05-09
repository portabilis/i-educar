<?php

namespace App\Models;

use iEducar\Modules\Servidores\Model\TipoVinculo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    ];

    /**
     * @var string
     */
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

    public function schoolClassTeacherDisciplines(): HasMany
    {
        return $this->hasMany(LegacySchoolClassTeacherDiscipline::class, 'professor_turma_id');
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(LegacySchoolClass::class, 'turma_id');
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(LegacyPeriod::class, 'turno_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'servidor_id');
    }

    public function disciplines()
    {
        return $this->belongsToMany(LegacyDiscipline::class, 'modules.professor_turma_disciplina', 'professor_turma_id', 'componente_curricular_id');
    }
}
