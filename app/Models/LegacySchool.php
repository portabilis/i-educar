<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * LegacySchool
 *
 * @property LegacyInstitution $institution
 */
class LegacySchool extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.escola';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_escola';

    /**
     * @var array
     */
    protected $fillable = [
        'cod_escola',
        'ref_usuario_cad',
        'ref_cod_instituicao',
        'ref_cod_escola_rede_ensino',
        'sigla',
        'data_cadastro',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * Relacionamento com a instituição.
     *
     * @return BelongsTo
     */
    public function institution()
    {
        return $this->belongsTo(LegacyInstitution::class, 'ref_cod_instituicao');
    }

    /**
     * @return BelongsTo
     */
    public function person()
    {
        return $this->belongsTo(LegacyPerson::class, 'ref_idpes');
    }
}
