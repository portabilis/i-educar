<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EducacensoDegree extends Model
{
    /**
     * @var string
     */
    protected $table = 'modules.educacenso_curso_superior';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    const GRAU_TECNOLOGICO = 1;
    const GRAU_LICENCIATURA = 2;
    const GRAU_BACHARELADO = 3;
    const GRAU_SEQUENCIAL = 4;

    protected $fillable = [
        'curso_id',
        'nome',
        'classe_id',
        'user_id',
        'created_at',
        'grau_academico'
    ];
}
