<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Registration extends Model
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
