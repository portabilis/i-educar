<?php

namespace App\Models;

use App\Traits\Ativo;
use App\Traits\HasLegacyDates;

class LegacyAbandonmentType extends LegacyModel
{
    use Ativo;
    use HasLegacyDates;

    /**
     * @var string
     */
    protected $table = 'pmieducar.abandono_tipo';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_abandono_tipo';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_cod_instituicao',
        'ref_usuario_exc',
        'ref_usuario_cad',
        'nome',
        'data_exclusao',
        'ativo'
    ];
}
