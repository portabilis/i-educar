<?php

namespace App\Models;

use App\Traits\Ativo;
use Illuminate\Database\Eloquent\Model;

/**
 * Class LegacyDisciplineExemption
 *
 * @property LegacyRegistration $registration
 */
class LegacyExemptionType extends Model
{
    use Ativo;

    const CREATED_AT = "data_cadastro";
    const UPDATED_AT = null;

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
        'data_cadastro',
        'data_exclusao',
        'ativo',
        'ref_cod_instituicao',
    ];
}
