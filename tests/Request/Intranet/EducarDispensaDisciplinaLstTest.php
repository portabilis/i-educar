<?php

use Database\Factories\LegacyDisciplineExemptionFactory;
use Database\Factories\LegacyExemptionTypeFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserTypeFactory;
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

    $this->list = fn (array $query = []) => $this->get('/intranet/educar_dispensa_disciplina_lst.php?' . http_build_query($query + [
        'ref_cod_matricula' => $this->registration->getKey(),
    ]));
});

test('lista as dispensas da matrícula com componente, tipo e data', function () {
    $exemption = ($this->exemption)($this->arts, $this->medical, ['data_cadastro' => '2030-03-10 08:00:00']);

    ($this->list)()
        ->assertOk()
        ->assertSee('educar_dispensa_disciplina_det.php?' . ($this->keys)($exemption), false)
        ->assertSeeInOrder(['Artes Visuais', 'Atestado médico', '10/03/2030'])
        ->assertSee('educar_dispensa_disciplina_cad.php?ref_cod_matricula=' . $this->registration->getKey(), false)
        ->assertSee('educar_matricula_det.php?cod_matricula=' . $this->registration->getKey(), false);
});

test('filtra pelo motivo e pelo componente', function (Closure $query) {
    $wanted = ($this->exemption)($this->arts, $this->medical);
    $other = ($this->exemption)($this->botany, $this->report);

    ($this->list)($query->call($this))
        ->assertOk()
        ->assertSee('educar_dispensa_disciplina_det.php?' . ($this->keys)($wanted), false)
        ->assertDontSee('educar_dispensa_disciplina_det.php?' . ($this->keys)($other), false);
})->with([
    'motivo' => [fn () => ['ref_cod_tipo_dispensa' => $this->medical->getKey()]],
    'componente' => [fn () => ['ref_cod_disciplina' => $this->arts->getKey()]],
]);

test('não oferece o botão Novo para matrícula que não está em andamento nem a quem só pode visualizar', function () {
    ($this->exemption)($this->arts, $this->medical);

    $this->registration->update(['aprovado' => App_Model_MatriculaSituacao::APROVADO]);

    ($this->list)()->assertOk()->assertSee('Artes Visuais')->assertDontSee('educar_dispensa_disciplina_cad.php', false);

    $this->registration->update(['aprovado' => App_Model_MatriculaSituacao::EM_ANDAMENTO]);

    ($this->as)(false);

    ($this->list)()->assertOk()->assertSee('Artes Visuais')->assertDontSee('educar_dispensa_disciplina_cad.php', false);
});

test('volta para o detalhe da matrícula quando ela não tem enturmação ativa', function () {
    $registration = LegacyRegistrationFactory::new()->create(['ativo' => 1]);

    $this->get('/intranet/educar_dispensa_disciplina_lst.php?ref_cod_matricula=' . $registration->getKey())
        ->assertRedirect('educar_matricula_det.php?cod_matricula=' . $registration->getKey());
});

test('volta para a listagem de matrículas quando a matrícula não é informada', function () {
    $this->get('/intranet/educar_dispensa_disciplina_lst.php')->assertRedirect('educar_matricula_lst.php');
});

test('manda para o início quem não pode ver as dispensas ou é de biblioteca', function (Closure $user) {
    LegacyGeneralConfigurationFactory::new()->create($this->institution + ['active_on_ieducar' => true]);

    $this->actingAs($user->call($this));

    ($this->list)()->assertRedirect();
})->with([
    'nível biblioteca' => [fn () => LegacyUserFactory::new()->withAccess(628)->create($this->institution + [
        'ref_cod_tipo_usuario' => LegacyUserTypeFactory::new()->create(['nivel' => App_Model_NivelTipoUsuario::BIBLIOTECA]),
    ])],
]);
