<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacySchoolGradeDiscipline extends Model
{
    use HasFactory;

    protected $table = 'pmieducar.escola_serie_disciplina';

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
    public function getIdAttribute() {
        return $this->getRawOriginal('id') ?? $this->ref_cod_disciplina;
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
     * @return BelongsTo
     */
    public function discipline()
    {
        return $this->belongsTo(LegacyDiscipline::class, 'ref_cod_disciplina');
    }

    /**
     * Filtra por escola
     *
     * @param Builder $query
     * @param int|null $school
     * @return void
     */
    public function scopeWhereSchool(Builder $query, ?int $school): void
    {
        if ($school !== null) {
            $query->where('ref_ref_cod_escola',$school);
        }
    }

    /**
     * Filtra por escola
     *
     * @param Builder $query
     * @param int|null $grade
     * @return void
     */
    public function scopeWhereGrade(Builder $query, ?int $grade): void
    {
        if ($grade !== null) {
            $query->where('ref_ref_cod_serie',$grade);
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
        $query->join('componente_curricular as c','ref_cod_disciplina','c.id');
        $query->addSelect('nome as name');
    }
}
