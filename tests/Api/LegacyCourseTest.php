<?php

namespace Tests\Api;

use App\Models\LegacyCourse;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyEducationLevelFactory;
use Database\Factories\LegacyEducationTypeFactory;
use Database\Factories\LegacyRegimeTypeFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LegacyCourseTest extends TestCase
{
    use DatabaseTransactions;

    public function test_does_not_save_course_name_without_letters(): void
    {
        $user = LegacyUserFactory::new()->admin()->create();
        $this->actingAs($user);

        $course = LegacyCourseFactory::new()->make([
            'ref_cod_instituicao' => $user->ref_cod_instituicao,
            'ref_cod_tipo_regime' => LegacyRegimeTypeFactory::new()->create([
                'ref_cod_instituicao' => $user->ref_cod_instituicao,
            ])->getKey(),
            'ref_cod_nivel_ensino' => LegacyEducationLevelFactory::new()->create([
                'ref_cod_instituicao' => $user->ref_cod_instituicao,
            ])->getKey(),
            'ref_cod_tipo_ensino' => LegacyEducationTypeFactory::new()->create([
                'ref_cod_instituicao' => $user->ref_cod_instituicao,
            ])->getKey(),
            'nm_curso' => '123 - @#',
        ]);

        $payload = [
            'tipoacao' => 'Novo',
            'ref_cod_instituicao' => $course->ref_cod_instituicao,
            'ref_cod_tipo_regime' => $course->ref_cod_tipo_regime,
            'ref_cod_nivel_ensino' => $course->ref_cod_nivel_ensino,
            'ref_cod_tipo_ensino' => $course->ref_cod_tipo_ensino,
            'nm_curso' => $course->nm_curso,
            'sgl_curso' => $course->sgl_curso,
            'qtd_etapas' => $course->qtd_etapas,
            'hora_falta' => 45,
            'carga_horaria' => $course->carga_horaria,
            'modalidade_curso' => $course->modalidade_curso,
        ];

        $this->post('/intranet/educar_curso_cad.php', $payload)
            ->assertOk()
            ->assertSee("O campo 'Curso' deve conter ao menos uma letra.", false);

        $this->assertDatabaseMissing((new LegacyCourse)->getTable(), [
            'nm_curso' => $course->nm_curso,
        ]);
    }

    public function test_does_not_save_course_with_zero_workload(): void
    {
        $user = LegacyUserFactory::new()->admin()->create();
        $this->actingAs($user);

        $course = LegacyCourseFactory::new()->make([
            'ref_cod_instituicao' => $user->ref_cod_instituicao,
            'ref_cod_tipo_regime' => LegacyRegimeTypeFactory::new()->create([
                'ref_cod_instituicao' => $user->ref_cod_instituicao,
            ])->getKey(),
            'ref_cod_nivel_ensino' => LegacyEducationLevelFactory::new()->create([
                'ref_cod_instituicao' => $user->ref_cod_instituicao,
            ])->getKey(),
            'ref_cod_tipo_ensino' => LegacyEducationTypeFactory::new()->create([
                'ref_cod_instituicao' => $user->ref_cod_instituicao,
            ])->getKey(),
            'carga_horaria' => 0,
        ]);

        $payload = [
            'tipoacao' => 'Novo',
            'ref_cod_instituicao' => $course->ref_cod_instituicao,
            'ref_cod_tipo_regime' => $course->ref_cod_tipo_regime,
            'ref_cod_nivel_ensino' => $course->ref_cod_nivel_ensino,
            'ref_cod_tipo_ensino' => $course->ref_cod_tipo_ensino,
            'nm_curso' => $course->nm_curso,
            'sgl_curso' => $course->sgl_curso,
            'qtd_etapas' => $course->qtd_etapas,
            'hora_falta' => 45,
            'carga_horaria' => '0',
            'modalidade_curso' => $course->modalidade_curso,
        ];

        $this->post('/intranet/educar_curso_cad.php', $payload)
            ->assertOk()
            ->assertSee("O campo 'Carga Horária' deve ser maior que zero.", false);

        $this->assertDatabaseMissing((new LegacyCourse)->getTable(), [
            'nm_curso' => $course->nm_curso,
            'carga_horaria' => 0,
        ]);
    }
}
