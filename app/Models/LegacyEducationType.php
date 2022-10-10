<?php

namespace App\Models;

use App\Traits\Ativo;
use App\Traits\HasLegacyDates;

class LegacyEducationType extends LegacyModel
{
    use Ativo;
    use HasLegacyDates;

    /**
     * @var string
     */
    protected $table = 'pmieducar.tipo_ensino';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_tipo_ensino';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_usuario_cad',
        'ref_usuario_exc',
        'nm_tipo',
        'ativo',
        'ref_cod_instituicao',
        'atividade_complementar',
        'data_exclusao'
    ];
}
