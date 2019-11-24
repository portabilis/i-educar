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

    protected $fillable = [
        'curso_id',
        'nome',
        'classe_id',
        'user_id',
        'created_at',
        'grau_academico'
    ];
}
