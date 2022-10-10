<?php

namespace App\Models;

use App\Traits\Ativo;
use App\Traits\HasLegacyDates;

class LegacyRegistrationDisciplinaryOccurrenceType extends LegacyModel
{
    use Ativo;
    use HasLegacyDates;

    /**
     * @var string
     */
    protected $table = 'pmieducar.matricula_ocorrencia_disciplinar';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_ocorrencia_disciplinar';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_cod_matricula',
        'ref_cod_tipo_ocorrencia_disciplinar',
        'sequencial',
        'ref_usuario_exc',
        'ref_usuario_cad',
        'observacao',
        'data_exclusao',
        'ativo',
        'visivel_pais',
        'updated_at'
    ];
}
