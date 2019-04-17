<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * LegacySchoolClass
 *
 * @property int                $id
 * @property string             $name
 * @property int                $year
 * @property int                $school_id
 * @property int                $course_id
 * @property int                $grade_id
 * @property int                $vacancies
 * @property DateTime           $begin_academic_year
 * @property DateTime           $end_academic_year
 * @property LegacyCourse       $course
 * @property LegacyLevel        $grade
 * @property LegacySchool       $school
 * @property LegacySchoolGrade  $schoolGrade
 * @property LegacyEnrollment[] $enrollments
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

    /**
     * @return int
     */
    public function getIdAttribute()
    {
        return $this->cod_turma;
    }

    /**
     * @return string
     */
    public function getNameAttribute()
    {
        return $this->nm_turma;
    }

    /**
     * @return int
     */
    public function getYearAttribute()
    {
        return $this->ano;
    }

    /**
     * @return int
     */
    public function getSchoolIdAttribute()
    {
        return $this->ref_ref_cod_escola;
    }

    /**
     * @return int
     */
    public function getCourseIdAttribute()
    {
        return $this->ref_cod_curso;
    }

    /**
     * @return int
     */
    public function getGradeIdAttribute()
    {
        return $this->ref_ref_cod_serie;
    }

    /**
     * @return int
     */
    public function getVacanciesAttribute()
    {
        $enrollments = $this->enrollments()
            ->where('ativo', 1)
            ->whereHas('registration', function ($query) {
                $query->where('dependencia', false);
            })->count();

        $vacancies = $this->max_aluno - $enrollments;

        return $vacancies > 0 ? $vacancies : 0;
    }

    public function stages()
    {
        if ($this->course->is_standard_calendar) {
            return $this->hasMany(LegacyAcademicYearStage::class, 'ref_ref_cod_escola', 'ref_ref_cod_escola')
                ->where('ref_ano', $this->year)
                ->orderBy('sequencial');
        }

        return $this->hasMany(LegacySchoolClassStage::class, 'ref_cod_turma', 'cod_turma')
            ->orderBy('sequencial');
    }

    /**
     * @return string
     */
    public function getBeginAcademicYearAttribute()
    {
        return $this->stages()->first()->data_inicio;
    }

    /**
     * @return string
     */
    public function getEndAcademicYearAttribute()
    {
        return $this->stages()->orderByDesc('sequencial')->first()->data_fim;
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
     * Relacionamento com as enturmações.
     *
     * @return HasMany
     */
    public function enrollments()
    {
        return $this->hasMany(LegacyEnrollment::class, 'ref_cod_turma', 'cod_turma');
    }

    /**
     * @return Collection
     */
    public function getActiveEnrollments()
    {
        return $this->enrollments()
            ->with([
                'registration' => function ($query) {
                    /** @var Builder $query */
                    $query->where('ano', $this->year);
                    $query->whereIn('aprovado', [1, 2, 3]);
                    $query->with('student.person');
                }
            ])
            ->where('ativo', 1)
            ->orderBy('sequencial_fechamento')
            ->get();
    }

    /**
     * @return BelongsTo
     */
    public function schoolGrade()
    {
        $belongsTo = $this->belongsTo(LegacySchoolGrade::class, 'ref_ref_cod_escola', 'ref_cod_escola')
            ->where('ref_cod_serie', $this->grade_id);

        return $belongsTo;
    }

    /**
     * Indica se bloqueia enturmações quando não houver vagas.
     *
     * @return bool
     */
    public function denyEnrollmentsWhenNoVacancy()
    {
        $schoolGrade = $this->schoolGrade;

        if (empty($schoolGrade)) {
            return true;
        }

        if (empty($schoolGrade->bloquear_enturmacao_sem_vagas)) {
            return true;
        }

        return (boolean) $schoolGrade->bloquear_enturmacao_sem_vagas;
    }
}
