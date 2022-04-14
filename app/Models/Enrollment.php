<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
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
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return SchoolClass
     */
    public function schoolClass()
    {
        return $this->belongsTo(LegacySchoolClass::class, 'ref_cod_turma', 'cod_turma');
    }

    /**
     * @return Registration
     */
    public function registration()
    {
        return $this->belongsTo(Registration::class, 'ref_cod_matricula', 'cod_matricula');
    }
}
