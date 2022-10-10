<?php

namespace App\Models;

use App\Traits\Ativo;
use App\Traits\HasLegacyDates;

class LegacyRegimeType extends LegacyModel
{
    use Ativo;
    use HasLegacyDates;

    /**
     * @var string
     */
    protected $table = 'pmieducar.tipo_regime';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_tipo_regime';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_usuario_exc',
        'ref_usuario_cad',
        'nm_tipo',
        'data_exclusao',
        'ativo',
        'ref_cod_instituicao',
    ];
}
