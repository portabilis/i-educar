<?php

namespace App\Models;

class LegacyAverageFormula extends LegacyModel
{
    protected $table = 'modules.formula_media';

    protected $fillable = [
        'instituicao_id',
        'nome',
        'formula_media',
        'tipo_formula',
        'substitui_menor_nota_rc',
    ];

    public $timestamps = false;

    public array $legacy = [
        'institution_id' => 'instituicao_id',
        'name' => 'nome',
        'average' => 'formula_media',
        'type' => 'tipo_formula',
        'replace_minor_note_rc' => 'substitui_menor_nota_rc',
    ];
}
