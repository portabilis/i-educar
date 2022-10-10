<?php

namespace App\Models;

use App\Traits\Ativo;
use App\Traits\HasLegacyDates;

class LegacyEducationLevel extends LegacyModel
{
    use Ativo;
    use HasLegacyDates;

    /**
     * @var string
     */
    protected $table = 'pmieducar.nivel_ensino';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_nivel_ensino';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_usuario_exc',
        'ref_usuario_cad',
        'nm_nivel',
        'descricao',
        'data_exclusao',
        'ativo',
        'ref_cod_instituicao',
    ];
}
