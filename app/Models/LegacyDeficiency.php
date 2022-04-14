<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyDeficiency extends Model
{
    /**
     * @var string
     */
    protected $table = 'cadastro.deficiencia';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_deficiencia';

    /**
     * @var bool
     */
    public $timestamps = false;

    protected $fillable = [
        'nm_deficiencia', 'deficiencia_educacenso', 'desconsidera_regra_diferenciada', 'exigir_laudo_medico',
    ];
}
