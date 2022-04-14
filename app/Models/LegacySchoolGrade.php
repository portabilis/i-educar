<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacySchoolGrade extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.escola_serie';

    /**
     * @var string
     */
    protected $primaryKey = 'ref_cod_escola';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_cod_escola',
        'ref_cod_serie',
        'ref_usuario_cad',
        'data_cadastro',
        'anos_letivos',
        'hora_inicial',
        'hora_final',
        'hora_inicio_intervalo',
        'hora_fim_intervalo',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return int
     */
    public function getSchoolIdAttribute()
    {
        return $this->ref_cod_escola;
    }

    /**
     * @return int
     */
    public function getGradeIdAttribute()
    {
        return $this->ref_cod_serie;
    }

    /**
     * Relacionamento com a série.
     *
     * @return BelongsTo
     */
    public function grade()
    {
        return $this->belongsTo(LegacyLevel::class, 'ref_cod_serie');
    }
}
