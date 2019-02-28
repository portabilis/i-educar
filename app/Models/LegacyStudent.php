<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyStudent extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.aluno';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_aluno';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_idpes', 'data_cadastro',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
