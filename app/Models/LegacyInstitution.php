<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;

/**
 * LegacyInstitution
 *
 * @property DateTime $relocation_date Data base para remanejamento
 */
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
     * @var array
     */
    protected $dates = [
        'data_base_remanejamento'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    public function getNameAttribute()
    {
        return $this->nm_instituicao;
    }

    /**
     * @return DateTime
     */
    public function getRelocationDateAttribute()
    {
        return $this->data_base_remanejamento;
    }
}
