<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * LegacyEnrollment
 *
 * @property int                $id
 * @property int                $registration_id
 * @property int                $school_class_id
 * @property string             $studentName
 * @property DateTime           $date
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
    protected $primaryKey = 'id';

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
        'sequencial_fechamento',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'data_enturmacao', 'data_exclusao'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return DateTime
     */
    public function getDateAttribute()
    {
        return $this->data_enturmacao;
    }

    /**
     * @return DateTime
     */
    public function getDateDepartedAttribute()
    {
        return $this->data_exclusao;
    }

    /**
     * @return int
     */
    public function getSchoolClassIdAttribute()
    {
        return $this->ref_cod_turma;
    }

    /**
     * @return int
     */
    public function getRegistrationIdAttribute()
    {
        return $this->ref_cod_matricula;
    }

    /**
     * @return string
     */
    public function getStudentNameAttribute()
    {
        return $this->registration->student->person->nome;
    }

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
