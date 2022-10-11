<?php

namespace App\Models;

use App\Traits\Ativo;
use App\Traits\HasLegacyDates;

class LegacyBenefit extends LegacyModel
{
    use Ativo;
    use HasLegacyDates;

    /**
     * @var string
     */
    protected $table = 'pmieducar.aluno_beneficio';
    /**
     * @var string
     */
    protected $primaryKey = 'cod_aluno_beneficio';
    /**
     * @var array
     */
    protected $fillable = [
        'ref_usuario_exc',
        'ref_usuario_cad',
        'nm_beneficio',
        'desc_beneficio',
        'data_exclusao',
        'ativo',
    ];

    public function students()
    {
        return $this->belongsToMany(LegacyStudent::class, 'pmieducar.aluno_aluno_beneficio', 'aluno_beneficio_id', 'aluno_id');
    }
}
