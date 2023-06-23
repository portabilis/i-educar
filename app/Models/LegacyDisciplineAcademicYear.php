<?php

namespace App\Models;

use App\Models\Builders\LegacyDisciplineAcademicYearBuilder;
use App\Traits\LegacyAttribute;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * LegacyDisciplineAcademicYear
 *
 * @method static LegacyDisciplineAcademicYearBuilder query()
 */
class LegacyDisciplineAcademicYear extends Pivot
{
    use LegacyAttribute;

    protected $table = 'modules.componente_curricular_ano_escolar';

    protected $primaryKey = 'componente_curricular_id';

    /**
     * Builder dos filtros
     */
    protected string $builder = LegacyDisciplineAcademicYearBuilder::class;

    /**
     * Atributos legados para serem usados nas queries
     *
     * @var string[]
     */
    public array $legacy = [
        'id' => 'componente_curricular_id',
        'workload' => 'carga_horaria',
    ];

    protected $fillable = [
        'componente_curricular_id',
        'ano_escolar_id',
        'carga_horaria',
        'tipo_nota',
        'anos_letivos',
        'hora_falta',
    ];

    public $timestamps = false;

    public $incrementing = false;

    /**
     * Serie
     */
    public function grade(): BelongsTo
    {
        return $this->belongsTo(LegacyGrade::class, 'ano_escolar_id', 'cod_serie');
    }

    /**
     * Component Curricular
     */
    public function discipline(): BelongsTo
    {
        return $this->belongsTo(LegacyDiscipline::class, 'componente_curricular_id');
    }

    protected function id(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->componente_curricular_id,
        );
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->discipline?->name,
        );
    }

    protected function workload(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->carga_horaria,
        );
    }
}
