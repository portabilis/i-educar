<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluationRule extends Model
{
    /**
     * @var string
     */
    protected $table = 'modules.regra_avaliacao';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = [
        'instituicao_id', 'nome', 'formula_media_id', 'tipo_nota', 'tipo_progressao', 'tipo_presenca',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
