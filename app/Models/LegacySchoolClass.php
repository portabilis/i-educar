<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

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
    public function getVacanciesAttribute()
    {
        $vacancies = $this->max_alunos - $this->enrollments()->where('ativo', 1)->count();

        return $vacancies > 0 ? $vacancies : 0;
    }

    /**
     * @return string
     *
     * @throws \Exception
     */
    public function getBeginAcademicYearAttribute()
    {
        if ($this->course->is_standard_calendar) {
            $stage = DB::table('pmieducar.ano_letivo_modulo')
                ->where('ref_ano', $this->year)
                ->where('ref_ref_cod_escola', $this->school_id)
                ->orderBy('sequencial')
                ->first();

            return (new DateTime($stage->data_inicio))->format('d/m/Y');
        }

        $stage = DB::table('pmieducar.turma_modulo')
            ->where('ref_cod_turma', $this->id)
            ->orderBy('sequencial')
            ->first();

        return (new DateTime($stage->data_inicio))->format('d/m/Y');
    }

    /**
     * @return string
     *
     * @throws \Exception
     */
    public function getEndAcademicYearAttribute()
    {
        if ($this->course->is_standard_calendar) {
            $stage = DB::table('pmieducar.ano_letivo_modulo')
                ->where('ref_ano', $this->year)
                ->where('ref_ref_cod_escola', $this->school_id)
                ->orderByDesc('sequencial')
                ->first();

            return (new DateTime($stage->data_fim))->format('d/m/Y');
        }

        $stage = DB::table('pmieducar.turma_modulo')
            ->where('ref_cod_turma', $this->id)
            ->orderByDesc('sequencial')
            ->first();

        return (new DateTime($stage->data_fim))->format('d/m/Y');
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
}
