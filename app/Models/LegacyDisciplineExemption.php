<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacyDisciplineExemption extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.dispensa_disciplina';

    protected $primaryKey = 'cod_dispensa';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_cod_matricula',
        'ref_cod_disciplina',
        'ref_cod_escola',
        'ref_cod_serie',
        'ref_usuario_exc',
        'ref_usuario_cad',
        'ref_cod_tipo_dispensa',
        'data_cadastro',
        'data_exclusao',
        'ativo',
        'observacao',
        'cod_dispensa',
        'updated_at',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * Relação com a matrícula.
     *
     * @return BelongsTo
     */
    public function registration()
    {
        return $this->belongsTo(LegacyRegistration::class, 'ref_cod_matricula');
    }
}
