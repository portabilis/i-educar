<?php

namespace App\Models;

use App\Traits\HasInstitution;
use App\Traits\HasLegacyDates;
use App\Traits\HasLegacyUserAction;
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

    public function student(): BelongsTo
    {
        return $this->belongsTo(LegacyStudent::class, 'ref_cod_aluno');
    }

    public function school(): void
    {
        $this->belongsTo(LegacySchool::class, 'ref_cod_escola');
    }
}
