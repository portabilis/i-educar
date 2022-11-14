<?php

namespace App\Models;

class LegacyDeficiency extends LegacyModel
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
        'nm_deficiencia',
        'deficiencia_educacenso',
        'desconsidera_regra_diferenciada',
        'exigir_laudo_medico',
    ];

    public array $legacy = [
        'id' => 'cod_deficiencia',
        'name' => 'nm_deficiencia',
        'educacenso' => 'deficiencia_educacenso',
        'disregards_different_rule' => 'desconsidera_regra_diferenciada',
        'require_medical_report' => 'exigir_laudo_medico'
    ];
}
