<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyDisciplineDependence extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.disciplina_dependencia';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_disciplina_dependencia';

    /**
     * @var bool
     */
    public $timestamps = false;
}
