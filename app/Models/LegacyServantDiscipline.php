<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyServantDiscipline extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.servidor_disciplina';

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
        'ref_cod_disciplina',
        'ref_ref_cod_instituicao',
        'ref_cod_servidor',
        'ref_cod_curso',
        'ref_cod_funcao',
    ];
}
