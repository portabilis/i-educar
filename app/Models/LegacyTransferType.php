<?php

namespace App\Models;

use App\Traits\Ativo;
use App\Traits\HasLegacyDates;
use App\Traits\HasInstitution;

class LegacyTransferType extends LegacyModel
{
    use Ativo;
    use HasLegacyDates;
    use HasInstitution;

    /**
     * @var string
     */
    protected $table = 'pmieducar.transferencia_tipo';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_transferencia_tipo';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_usuario_exc',
        'ref_usuario_cad',
        'nm_tipo',
        'desc_tipo',
        'data_exclusao',
        'ativo',
    ];
}
