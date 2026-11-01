<?php

use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserTypeFactory;
use iEducar\Modules\Educacenso\Model\TipoAtendimentoAluno;
use Tests\Request\Support\EnrollmentScenario;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    $this->student = $this->scenario->student('Ana Alves');
    $this->registration = $this->scenario->registration(['ref_cod_aluno' => $this->student]);

    // Tipo de atendimento 5 na turma: atendimento educacional especializado.
    $this->aee = $this->scenario->schoolClass(['nm_turma' => 'Sala de Recursos', 'tipo_atendimento' => '{5}']);
    $this->enrollment = $this->scenario->enroll($this->registration, $this->aee);

    $this->url = "/intranet/educar_matricula_turma_tipo_aee_cad.php?ref_cod_matricula={$this->registration->getKey()}&ref_cod_aluno={$this->student->getKey()}";
    $this->field = "{$this->aee->getKey()}_{$this->enrollment->sequencial}_tipoatendimento";
    $this->types = array_keys(TipoAtendimentoAluno::getDescriptiveValues());
});

test('oferece os tipos de atendimento só para as turmas de AEE da matrícula', function () {
    $this->scenario->enroll($this->registration, $this->scenario->schoolClass(['nm_turma' => 'Turma Comum']), ['sequencial' => 2]);

    $this->get($this->url)
        ->assertOk()
        ->assertSee('Ana Alves')
        ->assertSee('Tipo de atendimento educacional especializado do aluno na turma Sala de Recursos')
        ->assertSee('Ensino de Sistema Braille')
        ->assertDontSee('na turma Turma Comum');
});

test('grava os tipos escolhidos na enturmação e volta para a matrícula', function () {
    $payload = LegacyFields::from($this->get($this->url)->assertOk()->getContent(), [
        $this->field => [$this->types[0], $this->types[2]],
    ]);

    $this->post($this->url, $payload)
        ->assertRedirect('educar_matricula_det.php?cod_matricula=' . $this->registration->getKey())
        ->assertSessionHas('legacy', 'Tipo do AEE do aluno atualizado com sucesso.<br>');

    expect($this->enrollment->refresh()->getRawOriginal('tipo_atendimento'))->toBe('{' . $this->types[0] . ',' . $this->types[2] . '}');
});

test('sem nenhum tipo escolhido limpa os que estavam gravados', function () {
    $this->enrollment->update(['tipo_atendimento' => '{' . $this->types[0] . '}']);

    $payload = LegacyFields::from($this->get($this->url)->assertOk()->getContent());

    unset($payload[$this->field]);

    $this->post($this->url, $payload)->assertRedirect('educar_matricula_det.php?cod_matricula=' . $this->registration->getKey());

    expect($this->enrollment->refresh()->getRawOriginal('tipo_atendimento'))->toBeNull();
});

test('volta para a listagem de matrículas do aluno quando a matrícula não existe', function () {
    $this->get("/intranet/educar_matricula_turma_tipo_aee_cad.php?ref_cod_matricula=999999999&ref_cod_aluno={$this->student->getKey()}")
        ->assertRedirect('educar_matricula_lst.php?ref_cod_aluno=' . $this->student->getKey());
});

test('volta para a listagem de matrículas do aluno quando o usuário só pode visualizar matrículas', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(578, modify: false, remove: false)->withAccess(688)
            ->create(['ref_cod_instituicao' => $this->scenario->institution])
    );

    $this->get($this->url)->assertRedirect('educar_matricula_lst.php?ref_cod_aluno=' . $this->student->getKey());
});

test('manda para o início quem não pode ver a tela ou é de biblioteca', function (Closure $user) {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $this->actingAs($user->call($this));

    $this->get($this->url)->assertRedirect('/intranet/index.php');
})->with([
    'sem acesso ao processo 688' => [fn () => LegacyUserFactory::new()->institutional()->withAccess(578)->create(['ref_cod_instituicao' => $this->scenario->institution])],
    'nível biblioteca' => [fn () => LegacyUserFactory::new()->withAccess(578)->withAccess(688)->create([
        'ref_cod_instituicao' => $this->scenario->institution,
        'ref_cod_tipo_usuario' => LegacyUserTypeFactory::new()->create(['nivel' => App_Model_NivelTipoUsuario::BIBLIOTECA]),
    ])],
]);
