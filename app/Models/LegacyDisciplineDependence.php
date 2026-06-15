<?php

namespace App\Models;

use App\Models\Builders\LegacyDisciplineDependenceBuilder;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static LegacyDisciplineDependenceBuilder query()
 */
class LegacyDisciplineDependence extends Model
{
    /** @use HasBuilder<LegacyDisciplineDependenceBuilder> */
    use HasBuilder;

    protected static string $builder = LegacyDisciplineDependenceBuilder::class;

    public const CREATED_AT = null;

    /**
     * @var string
     */
    protected $table = 'pmieducar.disciplina_dependencia';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_disciplina_dependencia';

    // PK sem sequence no banco — código gerado manualmente (MAX + 1)
    public $incrementing = false;

    public $fillable = [
        'ref_cod_matricula',
        'ref_cod_disciplina',
        'ref_cod_escola',
        'ref_cod_serie',
        'observacao',
        'cod_disciplina_dependencia',
    ];
}
