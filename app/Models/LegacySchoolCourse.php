<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacySchoolCourse extends Model
{
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
        'data_cadastro',
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
