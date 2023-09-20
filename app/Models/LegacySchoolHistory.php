<?php

namespace App\Models;

use App\Models\Builders\SchoolHistoryBuilder;
use App\Services\Reports\Util;
use App\Services\SchoolHistory\Objects\SchoolHistory;
use App\Traits\HasInstitution;
use App\Traits\HasLegacyDates;
use App\Traits\HasLegacyUserAction;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacySchoolHistory extends LegacyModel
{
    use HasInstitution;
    use HasLegacyDates;
    use HasLegacyUserAction;

    /**
     * @var string
     */
    protected $table = 'pmieducar.historico_escolar';

    protected string $builder = SchoolHistoryBuilder::class;

    protected $fillable = [
        'ref_cod_aluno',
        'sequencial',
        'ano',
        'carga_horaria',
        'dias_letivos',
        'escola',
        'escola_cidade',
        'escola_uf',
        'observacao',
        'aprovado',
        'data_exclusao',
        'ativo',
        'faltas_globalizadas',
        'nm_serie',
        'origem',
        'extra_curricular',
        'ref_cod_matricula',
        'import',
        'frequencia',
        'registro',
        'livro',
        'folha',
        'historico_grade_curso_id',
        'nm_curso',
        'aceleracao',
        'ref_cod_escola',
        'dependencia',
        'posicao',
    ];

    protected $hidden = [
        'grade'
    ];

    protected $appends = [
        'grade'
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(LegacyStudent::class, 'ref_cod_aluno');
    }


    public function disciplines(): BelongsTo
    {
        return $this->belongsTo(LegacySchoolHistoryDiscipline::class, 'ref_cod_aluno')->wheeColu;
    }

    public function school(): void
    {
        $this->belongsTo(LegacySchool::class, 'ref_cod_escola');
    }

    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn () => (new SchoolHistoryStatus())->getDescriptiveValues()[$this->aprovado]
        );
    }

    protected function workload(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->carga_horaria ? Util::format($this->carga_horaria, 1) : null
        );
    }

    protected function frequency(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->frequencia ? Util::format($this->frequencia, 1) : null
        );
    }

    protected function grade(): Attribute
    {
        return Attribute::make(
            get: function () {
                $gradeName = $this->nm_serie[0] ?? null;
                if (!is_numeric($gradeName)) {
                    return null;
                }

                return (int)$gradeName;
            }
        );
    }

    public function schoolHistoryGradeCourse(): BelongsTo
    {
        return $this->belongsTo(LegacySchoolHistoryGradeCourse::class,  'historico_grade_curso_id');
    }

    public function courseIsEja(): bool
    {
        return $this->historico_grade_curso_id === SchoolHistory::GRADE_EJA;
    }

    public function courseIsSerie(): bool
    {
        return $this->historico_grade_curso_id === SchoolHistory::GRADE_SERIE;
    }

    public function courseIsAno(): bool
    {
        return $this->historico_grade_curso_id === SchoolHistory::GRADE_ANO;
    }

    public function ejaIsReproved(): bool
    {
        return in_array($this->aprovado_eja, [
            SchoolHistoryStatus::REPROVED,
            SchoolHistoryStatus::REPROVED_BY_ABSENCE
        ], true);
    }

    public function ejaIsOnGoing(): bool
    {
        return $this->aprovado_eja === SchoolHistoryStatus::ONGOING;
    }

    public function isOnGoing(): bool
    {
        return $this->aprovado === SchoolHistoryStatus::ONGOING;
    }

    public function isReproved(): bool
    {
        return in_array($this->aprovado, [
            SchoolHistoryStatus::REPROVED,
            SchoolHistoryStatus::REPROVED_BY_ABSENCE
        ], true);
    }
}
