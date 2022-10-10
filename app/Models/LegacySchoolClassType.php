<?php

namespace App\Models;

use App\Traits\Ativo;
use App\Traits\HasLegacyDates;

class LegacySchoolClassType extends LegacyModel
{
    use Ativo;
    use HasLegacyDates;

    /**
     * @var string
     */
    protected $table = 'pmieducar.turma_tipo';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_turma_tipo';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_usuario_cad',
        'nm_tipo',
        'sgl_tipo',
        'ref_cod_instituicao',
        'ativo'
    ];
}
