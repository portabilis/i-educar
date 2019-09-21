<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class LegacyDisciplineExemption
 * @property LegacyRegistration $registration
 */
class LegacyExemptionType extends Model
{
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

    /**
     * @var bool
     */
    public $timestamps = false;

    public function __toString()
    {
        return $this->nm_tipo;
    }
}
