<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyServantTeachCourse extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.servidor_curso_ministra';

    /**
     * @var string
     */
    protected $primaryKey = 'ref_cod_servidor';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = [
        'ref_ref_cod_instituicao',
        'ref_cod_servidor',
        'ref_cod_curso',
    ];
}
