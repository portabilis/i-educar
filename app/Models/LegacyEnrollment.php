<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * LegacyEnrollment
 *
 * @property LegacyRegistration $registration
 * @property LegacySchoolClass  $schoolClass
 */
class LegacyEnrollment extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.matricula_turma';

    /**
     * @var string
     */
    protected $primaryKey = 'ref_cod_matricula';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_cod_matricula',
        'ref_cod_turma',
        'sequencial',
        'ref_usuario_cad',
        'data_cadastro',
        'data_enturmacao',
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

    /**
     * Relação com a turma.
     *
     * @return BelongsTo
     */
    public function schoolClass()
    {
        return $this->belongsTo(LegacySchoolClass::class, 'ref_cod_turma');
    }
}
