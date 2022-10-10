<?php

namespace App\Models;

use App\Traits\Ativo;
use App\Traits\HasLegacyDates;

/**
 * Class LegacyDisciplineExemption
 *
 * @property LegacyRegistration $registration
 */
class LegacyExemptionType extends LegacyModel
{
    use Ativo;
    use HasLegacyDates;

    /**
     * @var string
     */
    protected $table = 'pmieducar.tipo_dispensa';

    protected $primaryKey = 'cod_tipo_dispensa';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_usuario_exc',
        'ref_usuario_cad',
        'nm_tipo',
        'descricao',
        'data_exclusao',
        'ativo',
        'ref_cod_instituicao',
    ];
}
