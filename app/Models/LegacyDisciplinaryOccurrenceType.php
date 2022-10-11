<?php

namespace App\Models;

use App\Traits\Ativo;
use App\Traits\HasInstitution;

class LegacyDisciplinaryOccurrenceType extends LegacyModel
{
    use Ativo;
    use HasInstitution;

    public const CREATED_AT = 'data_cadastro';
    public const UPDATED_AT = null;

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
