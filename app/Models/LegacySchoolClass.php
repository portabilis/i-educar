<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * LegacySchoolClass
 *
 * @property LegacySchool $school
 */
class LegacySchoolClass extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.turma';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_turma';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_usuario_cad',
        'nm_turma',
        'sgl_turma',
        'max_aluno',
        'data_cadastro',
        'ref_cod_turma_tipo',
        'ref_ref_cod_escola',
        'ref_ref_cod_serie',
        'ref_cod_curso',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    public function getIdAttribute()
    {
        return $this->cod_turma;
    }

    public function getNameAttribute()
    {
        return $this->nm_turma;
    }

    public function getYearAttribute()
    {
        return $this->ano;
    }

    public function getVacanciesAttribute()
    {
        $vacancies = $this->max_alunos - $this->enrollments()->where('ativo', 1)->count();

        return $vacancies > 0 ? $vacancies : 0;
    }

    public function getBeginAcademicYearAttribute()
    {

    }

    public function getEndAcadamicYearAttribute()
    {

    }

    /**
     * @return BelongsTo
     */
    public function course()
    {
        return $this->belongsTo(LegacyCourse::class, 'ref_cod_curso');
    }

    /**
     * @return BelongsTo
     */
    public function grade()
    {
        return $this->belongsTo(LegacyLevel::class, 'ref_ref_cod_serie');
    }

    /**
     * Relacionamento com a escola.
     *
     * @return BelongsTo
     */
    public function school()
    {
        return $this->belongsTo(LegacySchool::class, 'ref_ref_cod_escola');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function enrollments()
    {
        return $this->hasMany(LegacyEnrollment::class, 'ref_cod_turma', 'cod_turma');
    }
}
