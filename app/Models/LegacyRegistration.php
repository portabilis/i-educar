<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyRegistration extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.matricula';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_matricula';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_cod_aluno', 'data_cadastro', 'ano', 'ref_usuario_cad',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
