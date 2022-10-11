<?php

namespace App\Models;

use App\Traits\Ativo;
use App\Traits\HasLegacyDates;
use App\Traits\HasInstitution;

class LegacyRegimeType extends LegacyModel
{
    use Ativo;
    use HasInstitution;
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
    ];
}
