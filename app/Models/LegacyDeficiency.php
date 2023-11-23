<?php

namespace App\Models;

use App\Models\Builders\LegacyDeficiencyBuilder;

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

    public string $builder = LegacyDeficiencyBuilder::class;

    protected $fillable = [
        'nm_deficiencia',
        'deficiencia_educacenso',
        'deficiency_type_id',
        'desconsidera_regra_diferenciada',
        'exigir_laudo_medico',
    ];

    public array $legacy = [
        'id' => 'cod_deficiencia',
        'name' => 'nm_deficiencia',
        'educacenso' => 'deficiencia_educacenso',
        'disregards_different_rule' => 'desconsidera_regra_diferenciada',
        'require_medical_report' => 'exigir_laudo_medico',
    ];

    /**
     * @return BelongsToMany
     */
    public function individuals()
    {
        return $this->belongsToMany(
            LegacyIndividual::class,
            'cadastro.fisica_deficiencia',
            'ref_cod_deficiencia',
            'ref_idpes'
        );
    }
}
