<?php

namespace App\Models;

use App\Traits\Ativo;
use App\Traits\HasLegacyDates;
use App\Traits\HasInstitution;

class LegacyDisciplinaryOccurrenceType extends LegacyModel
{
    use Ativo;
    use HasLegacyDates;
    use HasInstitution;

    /**
     * @var string
     */
    protected $table = 'pmieducar.tipo_ocorrencia_disciplinar';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_tipo_ocorrencia_disciplinar';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_usuario_exc',
        'ref_usuario_cad',
        'nm_tipo',
        'descricao',
        'max_ocorrencias',
        'data_exclusao',
        'ativo'
    ];
}
