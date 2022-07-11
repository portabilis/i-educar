<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class LegacyDisciplineAcademicYear extends Pivot
{
    use HasFactory;

    protected $table = 'modules.componente_curricular_ano_escolar';

    protected $primaryKey = 'componente_curricular_id';

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
        return $this->getRawOriginal('id') ?? $this->componente_curricular_id;
    }

    /**
     * @return string
     */
    public function getNameAttribute() {
        return $this->getRawOriginal('name') ?? $this->discipline->name;
    }

    /**
     * @return int
     */
    public function getWorkloadAttribute() {
        return $this->getRawOriginal('workload') ?? $this->carga_horaria;
    }


    /**
     * Filtra por curso
     *
     * @param Builder $query
     * @param int|null $course
     * @return void
     */
    public function scopeWhereCourse(Builder $query, ?int $course = null): void
    {
        if ($course !== null) {
            $query->whereHas('grade',function ($q) use($course){
                $q->whereCourse($course);
            });
        }
    }

    /**
     * Filtra por série
     *
     * @param Builder $query
     * @param int|null $grade
     * @return void
     */
    public function scopeWhereGrade(Builder $query, ?int $grade = null): void
    {
        if ($grade !== null) {
            $query->where('ano_escolar_id',$grade);
        }
    }

    /**
     * Filtra somente os distintos por id
     *
     * @param Builder $query
     * @return void
     */
    public function scopeDistinctDiscipline(Builder $query): void
    {
        $query->distinct('id');
    }

    /**
     * Faz join com Disciplina
     *
     * @param Builder $query
     * @return void
     */
    public function scopeAddSelectName(Builder $query): void
    {
        $query->join('componente_curricular','componente_curricular_id','id');
        $query->addSelect('nome as name');
    }
}
