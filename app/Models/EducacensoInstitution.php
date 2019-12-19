<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EducacensoInstitution extends Model
{
    /**
     * @var string
     */
    protected $table = 'modules.educacenso_ies';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        'ies_id',
        'nome',
        'dependencia_administrativa_id',
        'tipo_instituicao_id',
        'uf',
        'user_id',
        'created_at',
    ];
}
