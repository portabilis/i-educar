<?php

namespace App\Models;

use App\Models\Builders\LegacyDisciplineAcademicYearBuilder;
use App\Traits\LegacyAttribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class LegacyDisciplineAcademicYear extends Pivot
{
    use LegacyAttribute;

    protected $table = 'modules.componente_curricular_ano_escolar';

    protected $primaryKey = 'componente_curricular_id';

    /**
     * Builder dos filtros
     *
     * @var string
     */
    protected $builder = LegacyDisciplineAcademicYearBuilder::class;

    /**
     * Atributos legados para serem usados nas queries
     *
     * @var string[]
     */
    public $legacy = [
        'id' => 'componente_curricular_id',
        'workload' => 'carga_horaria'
    ];

    protected $fillable = [
        'componente_curricular_id',
        'ano_escolar_id',
        'carga_horaria',
        'tipo_nota',
        'anos_letivos',
    ];

    public $timestamps = false;

    public $incrementing = false;

    /**
     * Serie
     *
     * @return BelongsTo
     */
    public function grade(): BelongsTo
    {
        return $this->belongsTo(LegacyGrade::class,'ano_escolar_id','cod_serie');
    }

    /**
     * Component Curricular
     *
     * @return BelongsTo
     */
    public function discipline(): BelongsTo
    {
        return $this->belongsTo(LegacyDiscipline::class,'componente_curricular_id');
    }

    /**
     * @return int
     */
    public function getIdAttribute() {
        return $this->componente_curricular_id;
    }

    /**
     * @return string
     */
    public function getNameAttribute() {
        return $this->discipline->name ?? null;
    }

    /**
     * @return int
     */
    public function getWorkloadAttribute() {
        return $this->carga_horaria;
    }


    /**
     * Filtra por curso
     *
     * @param Builder $query
     * @param int $course
     * @return void
     */
    public function scopeWhereCourse(Builder $query, int $course): void
    {
        $query->whereHas('grade',function ($q) use($course){
            $q->whereCourse($course);
        });
    }

    /**
     * Filtra por série
     *
     * @param Builder $query
     * @param int $grade
     * @return void
     */
    public function scopeWhereGrade(Builder $query, int $grade ): void
    {
        $query->where('ano_escolar_id',$grade);
    }

    /**
     * Filtra somente os distintos por id
     *
     * @param Builder $query
     * @return void
     */
    public function scopeDistinctDiscipline(Builder $query): void
    {
        $query->distinct('componente_curricular_id');
    }
}
