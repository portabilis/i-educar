<?php

use Database\Factories\LegacyDisciplinaryOccurrenceTypeFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyRegistrationDisciplinaryOccurrenceTypeFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserTypeFactory;
use Tests\Request\Support\EnrollmentScenario;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    $this->registration = $this->scenario->registration(['ref_cod_aluno' => $this->scenario->student('Ana Alves')]);
    $this->scenario->enroll($this->registration, $this->scenario->schoolClass(['nm_turma' => 'Turma Alfa']));

    $this->type = fn (string $name) => LegacyDisciplinaryOccurrenceTypeFactory::new()->create(['institution_id' => $this->scenario->institution, 'name' => $name]);

    $this->occurrence = fn ($type, array $attributes = []) => LegacyRegistrationDisciplinaryOccurrenceTypeFactory::new()->create($attributes + [
        'ref_cod_matricula' => $this->registration,
        'ref_cod_tipo_ocorrencia_disciplinar' => $type,
    ]);

    $this->detail = fn ($occurrence) => "educar_matricula_ocorrencia_disciplinar_det.php?ref_cod_matricula={$this->registration->getKey()}"
        . "&ref_cod_tipo_ocorrencia_disciplinar={$occurrence->ref_cod_tipo_ocorrencia_disciplinar}&sequencial={$occurrence->sequencial}";

    $this->list = fn (array $query = []) => $this->get('/intranet/educar_matricula_ocorrencia_disciplinar_lst.php?' . http_build_query($query + [
        'ref_cod_matricula' => $this->registration->getKey(),
    ]));
});

test('lista as ocorrências da matrícula, da mais recente para a mais antiga, com tipo, série e turma', function () {
    $delay = ($this->occurrence)(($this->type)('Atraso'), ['sequencial' => 1]);
    $fight = ($this->occurrence)(($this->type)('Briga'), ['sequencial' => 2]);

    ($this->list)()
        ->assertOk()
        ->assertSee('Ana Alves')
        ->assertSeeInOrder([($this->detail)($fight), 'Briga', 'Primeiro Ano', 'Turma Alfa', ($this->detail)($delay), 'Atraso'], false)
        ->assertSee('educar_matricula_ocorrencia_disciplinar_cad.php?ref_cod_matricula=' . $this->registration->getKey(), false);
});

test('filtra pelo tipo de ocorrência', function () {
    $delay = ($this->occurrence)(($this->type)('Atraso'));
    $fight = ($this->occurrence)(($this->type)('Briga'));

    ($this->list)(['ref_cod_tipo_ocorrencia_disciplinar' => $delay->ref_cod_tipo_ocorrencia_disciplinar, 'ref_cod_escola' => $this->scenario->school->getKey()])
        ->assertOk()
        ->assertSee(($this->detail)($delay), false)
        ->assertDontSee(($this->detail)($fight), false);
});

test('não lista ocorrência excluída nem de outra matrícula', function () {
    $type = ($this->type)('Atraso');

    $deleted = ($this->occurrence)($type);
    $deleted->update(['ativo' => 0]);

    $other = ($this->occurrence)($type, ['ref_cod_matricula' => $this->scenario->registration(), 'sequencial' => 7]);

    ($this->list)()
        ->assertOk()
        ->assertDontSee(($this->detail)($deleted), false)
        ->assertDontSee("ref_cod_matricula={$other->ref_cod_matricula}&", false);
});

test('não oferece o botão Novo a quem só pode visualizar matrículas', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(578, modify: false, remove: false)->withAccess(681)
            ->create(['ref_cod_instituicao' => $this->scenario->institution])
    );

    ($this->list)()->assertOk()->assertSee('Ana Alves')->assertDontSee('educar_matricula_ocorrencia_disciplinar_cad.php', false);
});

test('volta para a listagem de matrículas quando a matrícula não é informada', function () {
    $this->get('/intranet/educar_matricula_ocorrencia_disciplinar_lst.php')->assertRedirect('educar_matricula_lst.php');
});

test('manda para o início quem não pode ver a tela ou é de biblioteca', function (Closure $user) {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $this->actingAs($user->call($this));

    ($this->list)()->assertRedirect('/intranet/index.php');
})->with([
    'sem acesso ao processo 681' => [fn () => LegacyUserFactory::new()->institutional()->withAccess(578)->create(['ref_cod_instituicao' => $this->scenario->institution])],
    'nível biblioteca' => [fn () => LegacyUserFactory::new()->withAccess(578)->withAccess(681)->create([
        'ref_cod_instituicao' => $this->scenario->institution,
        'ref_cod_tipo_usuario' => LegacyUserTypeFactory::new()->create(['nivel' => App_Model_NivelTipoUsuario::BIBLIOTECA]),
    ])],
]);
