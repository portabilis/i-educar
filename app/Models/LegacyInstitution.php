<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LegacyInstitution extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.instituicao';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_instituicao';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_usuario_cad', 'ref_idtlog', 'ref_sigla_uf', 'cep', 'cidade', 'bairro', 'logradouro', 'nm_responsavel',
        'data_cadastro', 'nm_instituicao',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return HasOne
     */
    public function generalConfiguration(): HasOne
    {
        return $this->hasOne(LegacyGeneralConfiguration::class, 'ref_cod_instituicao', 'cod_instituicao');
    }
}
