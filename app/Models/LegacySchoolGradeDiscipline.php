<?php

namespace App\Models;

use App\Models\Builders\LegacySchoolGradeDisciplineBuilder;
use App\Traits\LegacyAttribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * LegacySchoolGradeDiscipline
 *
 * @method static LegacySchoolGradeDisciplineBuilder query()
 */
class LegacySchoolGradeDiscipline extends Model
{
    use LegacyAttribute;

    protected $table = 'pmieducar.escola_serie_disciplina';

    /**
     * Builder dos filtros
     *
     * @var string
     */
    protected $builder = LegacySchoolGradeDisciplineBuilder::class;

    /**
     * Atributos legados para serem usados nas queries
     *
     * @var string[]
     */
    public $legacy = [
        'id' => 'ref_cod_disciplina',
        'workload' => 'carga_horaria'
    ];

    protected $fillable = [
        'ref_ref_cod_serie',
        'ref_ref_cod_escola',
        'ref_cod_disciplina',
        'ativo',
        'carga_horaria',
        'etapas_especificas',
        'etapas_utilizadas',
        'updated_at',
        'anos_letivos',
    ];

    public $timestamps = false;

    /**
     * @return int
     */
    public function getIdAttribute()
    {
        return $this->ref_cod_disciplina;
    }

    /**
     * @return string
     */
    public function getNameAttribute()
    {
        return $this->discipline->name ?? null;
    }

    /**
     * @return int
     */
    public function getWorkloadAttribute()
    {
        return $this->carga_horaria;
    }

    /**
     * @return BelongsTo
     */
    public function discipline()
    {
        return $this->belongsTo(LegacyDiscipline::class, 'ref_cod_disciplina');
    }
}
