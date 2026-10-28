<?php

use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserTypeFactory;
use Tests\Request\Support\EnrollmentScenario;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    $this->student = $this->scenario->student('Ana Alves');
    $this->registration = $this->scenario->registration(['ref_cod_aluno' => $this->student, 'data_matricula' => '2030-02-10']);
    $this->enrollment = $this->scenario->enroll($this->registration, $this->scenario->schoolClass(), ['data_enturmacao' => '2030-02-10']);

    $this->url = "/intranet/educar_saida_escola_cad.php?ref_cod_matricula={$this->registration->getKey()}&ref_cod_aluno={$this->student->getKey()}&escola=Escola+Aurora";
    $this->detail = 'educar_matricula_det.php?cod_matricula=' . $this->registration->getKey();

    $this->payload = fn (array $overrides = []) => LegacyFields::from($this->get($this->url)->assertOk()->getContent(), $overrides + [
        'data_saida_escola' => '15/05/2030',
        'observacao' => 'Mudou de cidade',
    ]);
});

test('abre com o aluno, a escola recebida na URL e a data de hoje', function () {
    $html = $this->get($this->url)->assertOk()->getContent();
    $fields = LegacyFields::from($html);

    expect($fields['tipoacao'])->toBe('Novo')
        ->and($html)->toContain('Ana Alves', 'Escola Aurora')
        ->and($fields['data_saida_escola'])->toBe(now()->format('d/m/Y'))
        ->and($fields['ref_cod_matricula'])->toBe((string) $this->registration->getKey())
        ->and($fields['ref_cod_aluno'])->toBe((string) $this->student->getKey());
});

test('registra a saída da escola na matrícula sem mexer na situação nem na enturmação', function () {
    $this->post($this->url, ($this->payload)())->assertRedirect($this->detail);

    expect($this->registration->refresh())
        ->aprovado->toBe(App_Model_MatriculaSituacao::EM_ANDAMENTO)
        ->observacao->toBe('Mudou de cidade')
        ->ref_usuario_exc->toBe($this->user->getKey())
        ->and((bool) $this->registration->saida_escola)->toBeTrue()
        ->and(substr((string) $this->registration->data_saida_escola, 0, 10))->toBe('2030-05-15')
        ->and($this->enrollment->refresh()->ativo)->toBe(1);
});

test('sem observação grava "Não informado"', function () {
    $this->post($this->url, ($this->payload)(['observacao' => '']))->assertRedirect($this->detail);

    expect($this->registration->refresh()->observacao)->toBe('Não informado');
});

test('recusa a observação com mais de 300 caracteres', function () {
    $this->post($this->url, ($this->payload)(['observacao' => str_repeat('a', 301)]))
        ->assertOk()
        ->assertSee('O campo observação deve conter no máximo 300 caracteres.');

    expect((bool) $this->registration->refresh()->saida_escola)->toBeFalse();
});

test('avisa quando a matrícula não vem no envio', function () {
    $this->post($this->url, ($this->payload)(['ref_cod_matricula' => '']))
        ->assertOk()
        ->assertSee('Saída da escola não pode ser realizada.');

    expect((bool) $this->registration->refresh()->saida_escola)->toBeFalse();
});

test('a ação de excluir não faz nada', function () {
    $this->post($this->url, ($this->payload)(['tipoacao' => 'Excluir']))->assertOk();

    expect((bool) $this->registration->refresh()->saida_escola)->toBeFalse();
});

test('defeito: a mensagem de sucesso não chega à sessão', function () {
    // A tela usa `$this->mensagem .=`, que a classe base não intercepta.
    $this->post($this->url, ($this->payload)())
        ->assertRedirect($this->detail)
        ->assertSessionMissing('legacy');
});

test('volta para a listagem de matrículas do aluno quando o usuário só pode visualizar matrículas', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(578, modify: false, remove: false)->withAccess(693)
            ->create(['ref_cod_instituicao' => $this->scenario->institution])
    );

    $this->get($this->url)->assertRedirect('educar_matricula_lst.php?ref_cod_aluno=' . $this->student->getKey());
});

test('manda para o início quem não pode ver a tela ou é de biblioteca', function (Closure $user) {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $this->actingAs($user->call($this));

    $this->get($this->url)->assertRedirect('/intranet/index.php');
})->with([
    'sem acesso ao processo 693' => [fn () => LegacyUserFactory::new()->institutional()->withAccess(578)->create(['ref_cod_instituicao' => $this->scenario->institution])],
    'nível biblioteca' => [fn () => LegacyUserFactory::new()->withAccess(578)->withAccess(693)->create([
        'ref_cod_instituicao' => $this->scenario->institution,
        'ref_cod_tipo_usuario' => LegacyUserTypeFactory::new()->create(['nivel' => App_Model_NivelTipoUsuario::BIBLIOTECA]),
    ])],
]);
