<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyEducationLevel extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.nivel_ensino';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_nivel_ensino';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_usuario_cad', 'nm_nivel', 'data_cadastro', 'ref_cod_instituicao',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
