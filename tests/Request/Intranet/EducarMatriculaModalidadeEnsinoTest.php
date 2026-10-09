<?php

use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserTypeFactory;
use Tests\Request\Support\EnrollmentScenario;
use Tests\Request\Support\LegacyFields;
use Tests\Request\Support\LegacyForm;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    $this->registration = $this->scenario->registration([
        'ref_cod_aluno' => $this->scenario->student('Ana Alves'),
        'modalidade_ensino' => clsPmieducarMatricula::MODELO_EAD,
    ]);

    $this->url = '/intranet/educar_matricula_modalidade_ensino.php?ref_cod_matricula=' . $this->registration->getKey();
});

test('abre com o aluno, as quatro modalidades e a atual escolhida', function () {
    $html = $this->get($this->url)->assertOk()->assertSee('Ana Alves')->getContent();

    expect(LegacyFields::from($html)['modalidade_ensino'])->toBe((string) clsPmieducarMatricula::MODELO_EAD)
        ->and(array_values(LegacyForm::from($html)->options('modalidade_ensino')))->toBe(['Presencial', 'Semipresencial', 'EAD', 'Off-line']);
});

test('grava a modalidade escolhida', function () {
    $payload = LegacyFields::from($this->get($this->url)->assertOk()->getContent(), [
        'modalidade_ensino' => clsPmieducarMatricula::MODELO_SEMIPRESENCIAL,
    ]);

    $this->post($this->url, $payload)->assertOk()->assertSee('Modalidade de ensino atualizado com sucesso.');

    expect($this->registration->refresh()->modalidade_ensino)->toBe(clsPmieducarMatricula::MODELO_SEMIPRESENCIAL);
});

test('volta para a listagem de matrículas quando a matrícula não existe', function () {
    $this->get('/intranet/educar_matricula_modalidade_ensino.php?ref_cod_matricula=999999999')
        ->assertRedirect('educar_matricula_lst.php?ref_cod_aluno=');
});

test('volta para a listagem de matrículas quando o usuário só pode visualizar matrículas', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(578, modify: false, remove: false)->withAccess(684)
            ->create(['ref_cod_instituicao' => $this->scenario->institution])
    );

    $this->get($this->url)->assertRedirect('educar_matricula_lst.php?ref_cod_aluno=');
});

test('manda para o início quem não pode ver a tela ou é de biblioteca', function (Closure $user) {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $this->actingAs($user->call($this));

    $this->get($this->url)->assertRedirect('/intranet/index.php');
})->with([
    'sem acesso ao processo 684' => [fn () => LegacyUserFactory::new()->institutional()->withAccess(578)->create(['ref_cod_instituicao' => $this->scenario->institution])],
    'nível biblioteca' => [fn () => LegacyUserFactory::new()->withAccess(578)->withAccess(684)->create([
        'ref_cod_instituicao' => $this->scenario->institution,
        'ref_cod_tipo_usuario' => LegacyUserTypeFactory::new()->create(['nivel' => App_Model_NivelTipoUsuario::BIBLIOTECA]),
    ])],
]);
