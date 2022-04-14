<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public const GRAU_TECNOLOGICO = 1;
    public const GRAU_LICENCIATURA = 2;
    public const GRAU_BACHARELADO = 3;
    public const GRAU_SEQUENCIAL = 4;

    protected $fillable = [
        'curso_id',
        'nome',
        'classe_id',
        'user_id',
        'created_at',
        'grau_academico'
    ];
}
