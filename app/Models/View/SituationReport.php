<?php

namespace App\Models\View;

use Illuminate\Database\Eloquent\Model;

class SituationReport extends Model
{
    protected $table = 'relatorio.view_situacao_relatorios';

    protected $primaryKey = 'cod_matricula';

    public $timestamps = false;
}
