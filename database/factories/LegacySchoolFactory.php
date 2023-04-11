<?php

namespace Database\Factories;

use App\Models\LegacyCourse;
use App\Models\LegacySchool;
use App\Models\LegacySchoolCourse;
use App_Model_ZonaLocalizacao;
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

    public function withPhone(): static
    {
        return $this->afterCreating(function (LegacySchool $school) {
            LegacyPhoneFactory::new()->create([
                'idpes' => $school->person,
                'tipo' => 1,
            ]);
        });
    }

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

    public function withCourse(LegacyCourse $course): static
    {
        return $this->afterCreating(function (LegacySchool $school) use ($course) {
            $schoolCourse = LegacySchoolCourseFactory::new()->create([
                'ref_cod_escola' => $school,
                'ref_cod_curso' => $course,
            ]);

            $course->grades->each(fn ($grade) => LegacySchoolGradeFactory::new()
                ->create([
                    'ref_cod_escola' => $school,
                    'ref_cod_serie' => $grade,
                    'anos_letivos' => $schoolCourse->anos_letivos,
                ]));
        });
    }

    public function withClassroomsForEachGrade(LegacyCourse $course): static
    {
        return $this->afterCreating(function (LegacySchool $school) use ($course) {
            $course->grades->each(fn ($grade) => LegacySchoolClassFactory::new()
                ->create([
                    'nm_turma' => $grade->name . ' A',
                    'sgl_turma' => mb_substr($grade->name, 0, 1),
                    'turma_turno_id' => LegacyPeriodFactory::new()->morning(),
                    'ref_ref_cod_escola' => $school,
                    'ref_ref_cod_serie' => $grade,
                    'ref_cod_curso' => $course,
                ]));
        });
    }
}
