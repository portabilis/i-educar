<?php

use App\Models\LegacyEnrollment;
use App\Process;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\EnrollmentScenario;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    $this->schoolClass = $this->scenario->schoolClass(['nm_turma' => 'Turma Alfa']);
    $this->enrollment = $this->scenario->enrolled('Bia Borges', $this->schoolClass, ['data_enturmacao' => '2030-03-01', 'sequencial' => 1], ['data_matricula' => '2030-02-01']);
    $this->registration = $this->enrollment->registration;

    $this->url = fn (LegacyEnrollment $enrollment) => '/intranet/educar_matricula_historico_cad.php?' . http_build_query([
        'ref_cod_matricula' => $enrollment->ref_cod_matricula,
        'ref_cod_turma' => $enrollment->ref_cod_turma,
        'sequencial' => $enrollment->sequencial,
    ]);
    $this->history = "/enrollment-history/{$this->registration->getKey()}";
    $this->form = fn (array $overrides = [], ?LegacyEnrollment $enrollment = null) => LegacyFields::from($this->get(($this->url)($enrollment ?? $this->enrollment))->assertOk()->getContent(), $overrides);
    $this->edit = fn (array $overrides = [], ?LegacyEnrollment $enrollment = null) => $this->post(($this->url)($enrollment ?? $this->enrollment), ($this->form)($overrides, $enrollment));
});

test('abre a enturmação com os dados da matrícula e a situação atual', function () {
    $this->enrollment->update(['ativo' => 0, 'data_exclusao' => '2030-06-30', 'transferido' => true]);

    $response = $this->get(($this->url)($this->enrollment))
        ->assertOk()
        ->assertSeeInOrder(['Ano', '2030', 'Escola', 'Escola Aurora', 'Nome do Aluno', 'Bia Borges', 'Sequencial', '1']);

    $payload = LegacyFields::from($response->getContent());

    expect($payload['tipoacao'])->toBe('Editar')
        ->and($payload['data_enturmacao'])->toBe('01/03/2030')
        ->and($payload['data_exclusao'])->toBe('30/06/2030')
        ->and($payload['matricula_situacao'])->toBe('transferido');
});

test('quem só visualiza o histórico volta para ele', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);
    $this->actingAs(LegacyUserFactory::new()->institutional()->withAccess(Process::ENROLLMENT_HISTORY, modify: false)->create());

    $this->get(($this->url)($this->enrollment))->assertRedirect($this->history);
});

test('valida as datas entre as enturmações da matrícula', function () {
    ($this->edit)(['data_exclusao' => '28/02/2030'])
        ->assertOk()
        ->assertSee('A data de saída não pode ser anterior a data de enturmação.');

    ($this->edit)(['matricula_situacao' => 'remanejado', 'data_exclusao' => ''])
        ->assertOk()
        ->assertSee('É necessário informar a data de saída ao selecionar uma situação.');

    $beta = $this->scenario->schoolClass(['nm_turma' => 'Turma Beta']);
    $next = $this->scenario->enroll($this->registration, $beta, ['data_enturmacao' => '2030-08-01', 'sequencial' => 2]);

    ($this->edit)(['data_exclusao' => '02/08/2030'])
        ->assertOk()
        ->assertSee('A data de saída não pode ser posterior a data de entrada da enturmação seguinte.');

    $this->enrollment->update(['data_exclusao' => '2030-07-31', 'ativo' => 0]);

    ($this->edit)(['data_enturmacao' => '30/07/2030'], $next)
        ->assertOk()
        ->assertSee('A data de enturmação não pode ser anterior a data de saída da enturmação antecessora.');
});

test('a última enturmação de uma matrícula transferida não sai depois da saída da matrícula', function () {
    $this->registration->update(['aprovado' => App_Model_MatriculaSituacao::TRANSFERIDO, 'data_cancel' => '2030-06-30']);

    ($this->edit)(['data_exclusao' => '15/07/2030'])
        ->assertOk()
        ->assertSee('A data de saída não pode ser posterior a data de saída da matricula.');
});

test('troca a data de enturmação e a data de saída sem mudar a situação', function () {
    ($this->edit)(['data_enturmacao' => '05/03/2030', 'data_exclusao' => '30/06/2030'])
        ->assertRedirect($this->history)
        ->assertSessionHas('legacy', 'Edição efetuada com sucesso.');

    $this->enrollment->refresh();
    expect($this->enrollment->data_enturmacao->format('Y-m-d'))->toBe('2030-03-05')
        ->and($this->enrollment->data_exclusao->format('Y-m-d'))->toBe('2030-06-30')
        ->and($this->enrollment->ref_usuario_exc)->toBe($this->user->getKey());
});

test('com uma situação e a data de saída, desenturma e marca a situação', function () {
    ($this->edit)(['matricula_situacao' => 'transferido', 'data_exclusao' => '30/06/2030'])
        ->assertRedirect($this->history);

    $this->enrollment->refresh();
    expect($this->enrollment->ativo)->toBe(0)
        ->and((bool) $this->enrollment->transferido)->toBeTrue()
        ->and($this->enrollment->data_exclusao->format('Y-m-d'))->toBe('2030-06-30');
});

test('exclui a enturmação e desfaz a marca de remanejamento na mesma turma da seguinte', function () {
    $this->enrollment->update(['remanejado_mesma_turma' => true]);
    $next = $this->scenario->enroll($this->registration, $this->schoolClass, ['data_enturmacao' => '2030-08-01', 'sequencial' => 2, 'remanejado_mesma_turma' => true]);

    $this->post(($this->url)($this->enrollment), ($this->form)(['tipoacao' => 'Excluir']))
        ->assertRedirect($this->history)
        ->assertSessionHas('legacy', 'Exclusão efetuada com sucesso.');

    expect(LegacyEnrollment::query()->whereKey($this->enrollment->getKey())->exists())->toBeFalse()
        ->and((bool) $next->refresh()->remanejado_mesma_turma)->toBeFalse();
});
