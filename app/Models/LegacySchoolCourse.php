<?php

namespace App\Models;

use App\Traits\HasLegacyDates;

class LegacySchoolCourse extends LegacyModel
{
    use HasLegacyDates;

    /**
     * @var string
     */
    protected $table = 'pmieducar.escola_curso';

    /**
     * @var string
     */
    protected $primaryKey = 'ref_cod_escola';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_cod_escola',
        'ref_cod_curso',
        'ref_usuario_exc',
        'ref_usuario_cad',
        'data_exclusao',
        'ativo',
        'autorizacao',
        'anos_letivos',
        'updated_at',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    public function school()
    {
        return $this->belongsTo(LegacySchool::class, 'ref_cod_escola');
    }

    public function course()
    {
        return $this->belongsTo(LegacyCourse::class, 'ref_cod_curso');
    }
}
