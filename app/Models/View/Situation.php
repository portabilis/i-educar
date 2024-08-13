<?php

namespace App\Models\View;

use App\Models\Builders\SituationBuilder;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Model;

class Situation extends Model
{
    /** @use HasBuilder<SituationBuilder> */
    use HasBuilder;

    protected $table = 'relatorio.view_situacao';

    protected $primaryKey = 'cod_matricula';

    public $timestamps = false;

    protected static string $builder = SituationBuilder::class;
}
