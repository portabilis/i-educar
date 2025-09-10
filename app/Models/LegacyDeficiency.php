<?php

namespace App\Models;

use App\Models\Builders\LegacyDeficiencyBuilder;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class LegacyDeficiency extends LegacyModel
{
    /** @use HasBuilder<LegacyDeficiencyBuilder> */
    use HasBuilder;

    protected $table = 'cadastro.deficiencia';

    protected $primaryKey = 'cod_deficiencia';

    public $timestamps = false;

    protected static string $builder = LegacyDeficiencyBuilder::class;

    protected $fillable = [
        'nm_deficiencia',
        'deficiencia_educacenso',
        'transtorno_educacenso',
        'deficiency_type_id',
        'desconsidera_regra_diferenciada',
        'exigir_laudo_medico',
    ];

    public array $legacy = [
        'id' => 'cod_deficiencia',
        'name' => 'nm_deficiencia',
        'educacenso' => 'deficiencia_educacenso',
        'transtorno_educacenso' => 'transtorno_educacenso',
        'disregards_different_rule' => 'desconsidera_regra_diferenciada',
        'require_medical_report' => 'exigir_laudo_medico',
    ];

    /**
     * @return BelongsToMany<LegacyIndividual, $this>
     */
    public function individuals(): BelongsToMany
    {
        return $this->belongsToMany(
            LegacyIndividual::class,
            'cadastro.fisica_deficiencia',
            'ref_cod_deficiencia',
            'ref_idpes'
        );
    }
}
