<?php

use Database\Factories\LegacyDisciplineExemptionFactory;
use Database\Factories\LegacyExemptionTypeFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Api\DiarioApiFakeDataTestTrait;

uses(DiarioApiFakeDataTestTrait::class);

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    // Matrícula enturmada numa turma com dois componentes vinculados à série e à escola.
    $enrollment = $this->getPromotionFromAverageAndAttendanceWithoutRetake();
    $this->schoolClass = $enrollment->schoolClass;
    $this->registration = $enrollment->registration;
    $this->createStages($this->schoolClass->school, 1);
    $this->createDisciplines($this->schoolClass, 2);

    [$this->arts, $this->botany] = $this->schoolClass->disciplines()->orderBy('id')->get()->all();
    $this->arts->update(['nome' => 'Artes Visuais']);
    $this->botany->update(['nome' => 'Botânica']);

    $this->institution = ['ref_cod_instituicao' => $this->schoolClass->ref_cod_instituicao];
    $this->medical = LegacyExemptionTypeFactory::new()->create($this->institution + ['nm_tipo' => 'Atestado médico']);
    $this->report = LegacyExemptionTypeFactory::new()->create($this->institution + ['nm_tipo' => 'Laudo']);

    $this->exemption = fn ($discipline, $type, array $attributes = []) => LegacyDisciplineExemptionFactory::new()->create($attributes + [
        'ref_cod_matricula' => $this->registration,
        'ref_cod_disciplina' => $discipline->getKey(),
        'ref_cod_escola' => $this->schoolClass->ref_ref_cod_escola,
        'ref_cod_serie' => $this->schoolClass->ref_ref_cod_serie,
        'ref_cod_tipo_dispensa' => $type,
    ]);

    $this->keys = fn ($exemption) => http_build_query([
        'ref_cod_matricula' => $this->registration->getKey(),
        'ref_cod_serie' => $this->schoolClass->ref_ref_cod_serie,
        'ref_cod_escola' => $this->schoolClass->ref_ref_cod_escola,
        'ref_cod_disciplina' => $exemption->ref_cod_disciplina,
    ]);

    // Um usuário institucional com o acesso dado ao processo de dispensas.
    $this->as = function (bool $modify) {
        LegacyGeneralConfigurationFactory::new()->create($this->institution + ['active_on_ieducar' => true]);

        $this->actingAs(LegacyUserFactory::new()->institutional()->withAccess(628, modify: $modify, remove: $modify)->create($this->institution));
    };

    $this->detail = fn ($exemption) => $this->get('/intranet/educar_dispensa_disciplina_det.php?' . ($this->keys)($exemption));
});

test('mostra a matrícula, o curso, a série, o componente, o tipo e a observação', function () {
    $exemption = ($this->exemption)($this->arts, $this->medical, ['observacao' => 'Atestado de 60 dias']);

    ($this->detail)($exemption)
        ->assertOk()
        ->assertSeeInOrder([
            'Matricula', (string) $this->registration->getKey(),
            'Curso', $this->schoolClass->course->nm_curso,
            'Série', $this->schoolClass->grade->nm_serie,
            'Componente Curricular', 'Artes Visuais',
            'Tipo Dispensa', 'Atestado médico',
            'Observação', 'Atestado de 60 dias',
        ])
        ->assertSee("educar_dispensa_disciplina_cad.php?ref_cod_matricula={$this->registration->getKey()}&ref_cod_disciplina={$this->arts->getKey()}", false);
});

test('sem observação não mostra a linha', function () {
    $exemption = ($this->exemption)($this->arts, $this->medical, ['observacao' => null]);

    ($this->detail)($exemption)->assertOk()->assertSee('Atestado médico')->assertDontSee('Observação');
});

test('não oferece editar para matrícula que não está em andamento nem a quem só pode visualizar', function () {
    $exemption = ($this->exemption)($this->arts, $this->medical);

    $this->registration->update(['aprovado' => App_Model_MatriculaSituacao::APROVADO]);

    ($this->detail)($exemption)->assertOk()->assertSee('Artes Visuais')->assertDontSee('educar_dispensa_disciplina_cad.php', false);

    $this->registration->update(['aprovado' => App_Model_MatriculaSituacao::EM_ANDAMENTO]);

    ($this->as)(false);

    ($this->detail)($exemption)->assertOk()->assertSee('Artes Visuais')->assertDontSee('educar_dispensa_disciplina_cad.php', false);
});

test('volta para a listagem de dispensas da matrícula quando a dispensa não existe', function () {
    $this->get('/intranet/educar_dispensa_disciplina_det.php?' . http_build_query([
        'ref_cod_matricula' => $this->registration->getKey(),
        'ref_cod_serie' => $this->schoolClass->ref_ref_cod_serie,
        'ref_cod_escola' => $this->schoolClass->ref_ref_cod_escola,
        'ref_cod_disciplina' => $this->botany->getKey(),
    ]))->assertRedirect('educar_dispensa_disciplina_lst.php?ref_cod_matricula=' . $this->registration->getKey());
});

test('volta para as dispensas da matrícula avisando quando faltam os códigos da dispensa', function () {
    $this->get('/intranet/educar_dispensa_disciplina_det.php?ref_cod_matricula=5')
        ->assertRedirect('educar_dispensa_disciplina_lst.php?ref_cod_matricula=5')
        ->assertSessionHas('error', 'Dispensa de componente curricular não encontrada. O registro não existe ou foi excluído. Confira os registros listados abaixo.');
});
