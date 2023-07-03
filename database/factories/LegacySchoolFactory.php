<?php

namespace Database\Factories;

use App\Models\LegacyCourse;
use App\Models\LegacyPeriod;
use App\Models\LegacySchool;
use App\Models\LegacySchoolClass;
use App_Model_ZonaLocalizacao;
use Database\Factories\Concerns\ReleaseAbsencesForStudentsFactory;
use Database\Factories\Concerns\ReleaseScoresForStudentsFactory;
use iEducar\Modules\Educacenso\Model\DependenciaAdministrativaEscola;
use iEducar\Modules\Educacenso\Model\Regulamentacao;
use iEducar\Modules\Educacenso\Model\SchoolManagerRole;
use iEducar\Modules\Educacenso\Model\SituacaoFuncionamento;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacySchoolFactory extends Factory
{
    protected $model = LegacySchool::class;

    public function definition(): array
    {
        return [
            'ref_usuario_cad' => fn () => LegacyUserFactory::new()->current(),
            'ref_cod_instituicao' => fn () => LegacyInstitutionFactory::new()->current(),
            'sigla' => $this->faker->asciify(),
            'data_cadastro' => now(),
            'ref_idpes' => fn () => LegacyOrganizationFactory::new()->create(),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'ativo' => 1, // ativo
            'situacao_funcionamento' => SituacaoFuncionamento::EM_ATIVIDADE,
            'dependencia_administrativa' => DependenciaAdministrativaEscola::MUNICIPAL,
            'regulamentacao' => Regulamentacao::SIM,
            'zona_localizacao' => App_Model_ZonaLocalizacao::URBANA,
            'ref_idpes_gestor' => LegacyEmployeeFactory::new()->current(),
            'cargo_gestor' => SchoolManagerRole::DIRETOR,
            'nao_ha_funcionarios_para_funcoes' => true,
        ];
    }

    /**
     * Altera o nome da escola.
     */
    public function withName(string $name): static
    {
        $person = LegacyPersonFactory::new()->create([
            'nome' => $name,
        ]);

        $organization = LegacyOrganizationFactory::new()->create([
            'idpes' => $person,
            'fantasia' => $name,
        ]);

        return $this->state([
            'ref_idpes' => $organization,
        ]);
    }

    /**
     * Adiciona telefone para a escola.
     */
    public function withPhone(): static
    {
        return $this->afterCreating(function (LegacySchool $school) {
            LegacyPhoneFactory::new()->create([
                'idpes' => $school->person,
                'tipo' => 1,
            ]);
        });
    }

    /**
     * Define o usuário "admin" como diretor da escola.
     */
    public function withAdminAsDirector(): static
    {
        return $this->afterCreating(function (LegacySchool $school) {
            SchoolManagerFactory::new()->create([
                'employee_id' => LegacyEmployeeFactory::new()->current(),
                'school_id' => $school,
                'chief' => true, // Gestor Principal
            ]);
        });
    }

    /**
     * Vincula o curso, suas séres e disciplinas à escola.
     */
    public function withCourse(LegacyCourse $course): static
    {
        return $this->afterCreating(function (LegacySchool $school) use ($course) {
            $schoolCourse = LegacySchoolCourseFactory::new()->create([
                'ref_cod_escola' => $school,
                'ref_cod_curso' => $course,
            ]);

            $course->grades->each(fn ($grade) => LegacySchoolGradeFactory::new()
                ->withDisciplines()
                ->create([
                    'ref_cod_escola' => $school,
                    'ref_cod_serie' => $grade,
                    'anos_letivos' => $schoolCourse->anos_letivos,
                ]));
        });
    }

    /**
     * Adiciona uma turma para o turno e para cada série da escola.
     */
    public function withClassroomsForEachGrade(LegacyCourse $course, LegacyPeriod $period): static
    {
        return $this->afterCreating(function (LegacySchool $school) use ($course, $period) {
            $course->grades->each(fn ($grade) => LegacySchoolClassFactory::new()
                ->create([
                    'nm_turma' => $grade->name . ' ' . $period->name,
                    'sgl_turma' => mb_substr($grade->name, 0, 1),
                    'turma_turno_id' => $period,
                    'ref_ref_cod_escola' => $school,
                    'ref_ref_cod_serie' => $grade,
                    'ref_cod_curso' => $course,
                    'max_aluno' => 20,
                ]));
        });
    }

    /**
     * Adiciona alunos para cada turma da escola.
     */
    public function withStudentsForEachClassrooms(int $count = 1): static
    {
        return $this->afterCreating(function (LegacySchool $school) use ($count) {
            $school->schoolClasses->each(function (LegacySchoolClass $schoolClass) use ($count) {
                for ($i = 0; $i < $count; $i++) {
                    $random = $this->faker->randomElement([0, 1, -1]);

                    $individual = LegacyIndividualFactory::new()
                        ->father()
                        ->mother()
                        ->withAge($schoolClass->grade->idade_inicial + $random)
                        ->create();

                    $student = LegacyStudentFactory::new()->create([
                        'ref_idpes' => $individual,
                    ]);

                    LegacyRegistrationFactory::new()
                        ->withStudent($student)
                        ->withEnrollment($schoolClass)
                        ->create();
                }
            });
        });
    }

    /**
     * Lança notas para cada aluno da escola.
     */
    public function withScoresForEachStudent(): static
    {
        return $this->afterCreating(function (LegacySchool $school) {
            ReleaseScoresForStudentsFactory::fromSchool($school);
        });
    }

    /**
     * Lança faltas para cada aluno da escola.
     */
    public function withAbsencesForEachStudent(): static
    {
        return $this->afterCreating(function (LegacySchool $school) {
            ReleaseAbsencesForStudentsFactory::fromSchool($school);
        });
    }

    /**
     * Inicia um ano letivo do tipo "semestre".
     */
    public function withSemesterAsStageType(): static
    {
        return $this->afterCreating(function (LegacySchool $school) {
            $semester = LegacyStageTypeFactory::new()->semester();

            LegacySchoolAcademicYearFactory::new()
                ->withStageType($semester)
                ->withSchool($school)
                ->create();
        });
    }

    /**
     * Inicia um ano letivo do tipo "bimestre".
     */
    public function withBimonthlyAsStageType(): static
    {
        return $this->afterCreating(function (LegacySchool $school) {
            $bimonthly = LegacyStageTypeFactory::new()->bimonthly();

            LegacySchoolAcademicYearFactory::new()
                ->withStageType($bimonthly)
                ->withSchool($school)
                ->create();
        });
    }
}
