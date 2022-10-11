<?php

namespace App\Models;

use App\Traits\HasInstitution;
use App\Traits\HasLegacyDates;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacySchoolHistory extends LegacyModel
{
    use HasInstitution;
    use HasLegacyDates;

    /**
     * @var string
     */
    protected $table = 'pmieducar.historico_escolar';

    protected $fillable = [
        'ref_cod_aluno',
        'sequencial',
        'ref_usuario_exc',
        'ref_usuario_cad',
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

    /**
     * @return BelongsTo
     */
    public function student()
    {
        return $this->belongsTo(LegacyStudent::class, 'cod_aluno');
    }
}
