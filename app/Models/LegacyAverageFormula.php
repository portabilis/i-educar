<?php

namespace App\Models;

class LegacyAverageFormula extends LegacyModel
{
    /**
     * @var string
     */
    protected $table = 'modules.formula_media';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = [
        'instituicao_id',
        'nome',
        'formula_media',
        'tipo_formula',
        'substitui_menor_nota_rc',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    public array $legacy = [
        'institution_id' => 'instituicao_id',
        'name' => 'nome',
        'average' => 'formula_media',
        'type' => 'tipo_formula',
        'replace_minor_note_rc' => 'substitui_menor_nota_rc',
    ];
}
