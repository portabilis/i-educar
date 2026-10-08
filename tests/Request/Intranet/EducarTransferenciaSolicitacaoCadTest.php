<?php

use App\Models\LegacyDisciplineScoreAverage;
use App\Models\LegacyGeneralConfiguration;
use App\Models\LegacyTransferRequest;
use Database\Factories\LegacyActiveLookingFactory;
use Database\Factories\LegacyDisciplineScoreAverageFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyRegistrationScoreFactory;
use Database\Factories\LegacyTransferRequestFactory;
use Database\Factories\LegacyTransferTypeFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserTypeFactory;
use iEducar\Modules\School\Model\ActiveLooking;
use Illuminate\Support\Facades\DB;
use Tests\Request\Support\EnrollmentScenario;
use Tests\Request\Support\LegacyFields;
use Tests\Request\Support\LegacyForm;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    $this->institution = ['ref_cod_instituicao' => $this->scenario->institution];

    // O listener do aviso de transferência lê a configuração geral da instituição.
    LegacyGeneralConfigurationFactory::new()->create($this->institution + ['active_on_ieducar' => true]);

    $this->student = $this->scenario->student('Ana Alves');
    $this->registration = $this->scenario->registration(['ref_cod_aluno' => $this->student, 'data_matricula' => '2030-02-10']);
    $this->enrollment = $this->scenario->enroll($this->registration, $this->scenario->schoolClass(), ['data_enturmacao' => '2030-02-10']);
    $this->type = LegacyTransferTypeFactory::new()->create($this->institution + ['nm_tipo' => 'Mudança de endereço']);

    $this->url = "/intranet/educar_transferencia_solicitacao_cad.php?ref_cod_matricula={$this->registration->getKey()}&ref_cod_aluno={$this->student->getKey()}";
    $this->detail = 'educar_matricula_det.php?cod_matricula=' . $this->registration->getKey();

    $this->payload = fn (array $overrides = []) => LegacyFields::from($this->get($this->url)->assertOk()->getContent(), $overrides + [
        'ref_cod_transferencia_tipo' => $this->type->getKey(),
        'data_cancel' => '15/05/2030',
        'observacao' => 'A família mudou de bairro',
    ]);

    $this->request = fn () => LegacyTransferRequest::query()->where('ref_cod_matricula_saida', $this->registration->getKey())->first();
});

// A tela abre uma transação ao gravar e, em parte das recusas, devolve o
// formulário sem desfazê-la.
afterEach(function () {
    while (DB::transactionLevel() > 1) {
        DB::rollBack();
    }
});

test('abre com o aluno, os motivos ativos da instituição e a data de hoje', function () {
    LegacyTransferTypeFactory::new()->create($this->institution + ['nm_tipo' => 'Motivo extinto'])->update(['ativo' => 0]);

    $html = $this->get($this->url)->assertOk()->assertSee('Ana Alves')->getContent();

    expect(LegacyFields::from($html)['data_cancel'])->toBe(now()->format('d/m/Y'))
        ->and(array_values(array_filter(LegacyForm::from($html)->options('ref_cod_transferencia_tipo'))))->toBe(['Selecione', 'Mudança de endereço']);
});

test('solicita a transferência para uma escola da rede: transfere a matrícula e encerra a enturmação', function () {
    $destination = EnrollmentScenario::for($this->user, 2030)->school;

    $this->post($this->url, ($this->payload)(['ref_cod_escola' => $destination->getKey()]))
        ->assertRedirect($this->detail);

    expect($this->registration->refresh())
        ->aprovado->toBe(App_Model_MatriculaSituacao::TRANSFERIDO)
        ->ativo->toBe(1)
        ->ref_usuario_exc->toBe($this->user->getKey())
        ->and($this->registration->data_cancel->format('Y-m-d'))->toBe('2030-05-15');

    expect($this->enrollment->refresh())
        ->ativo->toBe(0)
        ->and((bool) $this->enrollment->transferido)->toBeTrue()
        ->and($this->enrollment->data_exclusao->format('Y-m-d'))->toBe('2030-05-15');

    $request = ($this->request)();

    expect($request)
        ->ref_cod_transferencia_tipo->toBe($this->type->getKey())
        ->ref_cod_escola_destino->toBe($destination->getKey())
        ->observacao->toBe('A família mudou de bairro')
        ->ativo->toBe(1)
        ->ref_usuario_cad->toBe($this->user->getKey())
        ->escola_destino_externa->toBeNull()
        ->and($request->data_transferencia->format('Y-m-d'))->toBe(now()->format('Y-m-d'));
});

test('solicita a transferência para fora da rede, guardando escola, estado e município', function () {
    $this->post($this->url, ($this->payload)([
        'ref_cod_escola' => $this->scenario->school->getKey(),
        'escola_em_outro_municipio' => 'on',
        'escola_destino_externa' => 'Colégio Horizonte',
        'estado_escola_destino_externa' => 'Paraná',
        'municipio_escola_destino_externa' => 'Curitiba',
    ]))->assertRedirect($this->detail);

    expect(($this->request)())
        ->ref_cod_escola_destino->toBeNull()
        ->escola_destino_externa->toBe('Colégio Horizonte')
        ->estado_escola_destino_externa->toBe('Paraná')
        ->municipio_escola_destino_externa->toBe('Curitiba');
});

test('descarta os dados de escola externa quando o destino é da rede', function () {
    $this->post($this->url, ($this->payload)([
        'escola_destino_externa' => 'Colégio Horizonte',
        'estado_escola_destino_externa' => 'Paraná',
        'municipio_escola_destino_externa' => 'Curitiba',
    ]))->assertRedirect($this->detail);

    expect(($this->request)())
        ->escola_destino_externa->toBeNull()
        ->estado_escola_destino_externa->toBeNull()
        ->municipio_escola_destino_externa->toBeNull();
});

test('para a matrícula já concluída registra a solicitação sem mudar a situação', function () {
    $this->registration->update(['aprovado' => App_Model_MatriculaSituacao::APROVADO]);

    $this->post($this->url, ($this->payload)())->assertRedirect($this->detail);

    expect($this->registration->refresh()->aprovado)->toBe(App_Model_MatriculaSituacao::APROVADO)
        ->and($this->enrollment->refresh()->ativo)->toBe(1)
        ->and(($this->request)())->not->toBeNull();
});

test('leva a transferência para as médias lançadas e para a busca ativa em andamento', function () {
    $average = LegacyDisciplineScoreAverageFactory::new()->create([
        'nota_aluno_id' => LegacyRegistrationScoreFactory::new()->create(['matricula_id' => $this->registration]),
    ]);

    $looking = LegacyActiveLookingFactory::new()->create([
        'registration_id' => $this->registration,
        'result' => ActiveLooking::ACTIVE_LOOKING_IN_PROGRESS_RESULT,
        'end' => null,
    ]);

    $this->post($this->url, ($this->payload)())->assertRedirect($this->detail);

    $situation = LegacyDisciplineScoreAverage::query()
        ->where('nota_aluno_id', $average->nota_aluno_id)
        ->where('componente_curricular_id', $average->componente_curricular_id)
        ->value('situacao');

    expect($situation)->toBe(App_Model_MatriculaSituacao::TRANSFERIDO)
        ->and($looking->refresh()->result)->toBe(ActiveLooking::ACTIVE_LOOKING_TRANSFER_RESULT)
        ->and($looking->end->format('Y-m-d'))->toBe('2030-05-15');
});

// Defeito conhecido: o listener do aviso de transferência tem propriedades
// tipadas que só recebem valor quando a instituição tem configuração geral.
// Sem ela, a solicitação é gravada e a tela estoura logo depois.
test('defeito: sem configuração geral da instituição a solicitação é gravada e a tela estoura', function () {
    $payload = ($this->payload)();

    LegacyGeneralConfiguration::query()->where('ref_cod_instituicao', $this->scenario->institution)->delete();

    $this->post($this->url, $payload)->assertServerError();

    expect(($this->request)())->not->toBeNull();
});

test('recusa a data anterior à da matrícula', function (?string $registrationDate, string $created) {
    $this->registration->update(['data_matricula' => $registrationDate, 'data_cadastro' => $created]);

    $this->post($this->url, ($this->payload)(['data_cancel' => '05/02/2030']))
        ->assertOk()
        ->assertSee('Data de transferência não pode ser inferior a data da matrícula.');

    expect(($this->request)())->toBeNull();
})->with([
    'pela data da matrícula' => ['2030-02-10', '2030-01-01 08:00:00'],
    // Sem data de matrícula, vale a data de cadastro.
    'pela data de cadastro' => [null, '2030-02-10 08:00:00'],
]);

test('recusa a segunda solicitação para a mesma matrícula', function () {
    LegacyTransferRequestFactory::new()->create(['ref_cod_matricula_saida' => $this->registration, 'ref_cod_matricula_entrada' => null]);

    $this->post($this->url, ($this->payload)())->assertOk()->assertSee('Já existe uma solicitação de transferência ativa.');

    expect(LegacyTransferRequest::query()->where('ref_cod_matricula_saida', $this->registration->getKey())->count())->toBe(1);
});

test('sem motivo não registra a solicitação e desfaz a transferência da matrícula', function () {
    $this->post($this->url, ($this->payload)(['ref_cod_transferencia_tipo' => '']))->assertOk()->assertSee('Cadastro não realizado.');

    expect(($this->request)())->toBeNull()
        ->and($this->registration->refresh()->aprovado)->toBe(App_Model_MatriculaSituacao::EM_ANDAMENTO)
        ->and($this->enrollment->refresh()->ativo)->toBe(1);
});

test('cancela a solicitação de transferência', function () {
    $request = LegacyTransferRequestFactory::new()->create(['ref_cod_matricula_saida' => $this->registration, 'ref_cod_matricula_entrada' => null]);

    $looking = LegacyActiveLookingFactory::new()->create([
        'registration_id' => $this->registration,
        'result' => ActiveLooking::ACTIVE_LOOKING_TRANSFER_RESULT,
    ]);

    $this->get($this->url . '&cancela=1')
        ->assertRedirect($this->detail)
        ->assertSessionHas('legacy', 'Exclusão efetuada com sucesso.<br>');

    expect($request->refresh())
        ->ativo->toBe(0)
        ->ref_usuario_exc->toBe($this->user->getKey())
        ->and($looking->refresh()->result)->toBe(ActiveLooking::ACTIVE_LOOKING_IN_PROGRESS_RESULT)
        ->and($looking->end)->toBeNull();
});

test('ao cancelar reabrindo a matrícula, volta a situação para em andamento e reativa a última enturmação', function () {
    $this->registration->update(['aprovado' => App_Model_MatriculaSituacao::TRANSFERIDO, 'data_cancel' => '2030-05-15']);
    $this->enrollment->update(['ativo' => 0, 'transferido' => true, 'data_exclusao' => '2030-05-15']);

    LegacyTransferRequestFactory::new()->create(['ref_cod_matricula_saida' => $this->registration, 'ref_cod_matricula_entrada' => null]);

    $this->get($this->url . '&cancela=1&reabrir_matricula=1')->assertRedirect($this->detail);

    expect($this->registration->refresh())
        ->aprovado->toBe(App_Model_MatriculaSituacao::EM_ANDAMENTO)
        ->data_cancel->toBeNull()
        ->and($this->enrollment->refresh()->ativo)->toBe(1)
        ->and((bool) $this->enrollment->transferido)->toBeFalse()
        ->and($this->enrollment->data_exclusao)->toBeNull();
});

test('ao cancelar reabrindo uma matrícula sem enturmação, só volta a situação', function () {
    $registration = $this->scenario->registration(['ref_cod_aluno' => $this->student, 'aprovado' => App_Model_MatriculaSituacao::TRANSFERIDO, 'ano' => 2029]);

    LegacyTransferRequestFactory::new()->create(['ref_cod_matricula_saida' => $registration, 'ref_cod_matricula_entrada' => null]);

    $this->get("/intranet/educar_transferencia_solicitacao_cad.php?ref_cod_matricula={$registration->getKey()}&ref_cod_aluno={$this->student->getKey()}&cancela=1&reabrir_matricula=1")
        ->assertRedirect('educar_matricula_det.php?cod_matricula=' . $registration->getKey());

    expect($registration->refresh()->aprovado)->toBe(App_Model_MatriculaSituacao::EM_ANDAMENTO);
});

test('avisa quando não há solicitação a cancelar', function () {
    $this->get($this->url . '&cancela=1')
        ->assertOk()
        ->assertSee('Não foi possível encontrar a Solicitação de Transferência do Aluno.');
});

test('a quem não pode excluir, o link de cancelar só abre o formulário', function () {
    $request = LegacyTransferRequestFactory::new()->create(['ref_cod_matricula_saida' => $this->registration, 'ref_cod_matricula_entrada' => null]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(578, remove: false)->withAccess(691)->create($this->institution)
    );

    $this->get($this->url . '&cancela=1')->assertOk();

    expect($request->refresh()->ativo)->toBe(1);
});

test('volta para o detalhe da matrícula quando o usuário só pode visualizar matrículas', function () {
    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(578, modify: false, remove: false)->withAccess(691)->create($this->institution)
    );

    $this->get($this->url)->assertRedirect($this->detail);
});

test('manda para o início quem não pode ver a tela ou é de biblioteca', function (Closure $user) {
    $this->actingAs($user->call($this));

    $this->get($this->url)->assertRedirect('/intranet/index.php');
})->with([
    'sem acesso ao processo 691' => [fn () => LegacyUserFactory::new()->institutional()->withAccess(578)->create($this->institution)],
    'nível biblioteca' => [fn () => LegacyUserFactory::new()->withAccess(578)->withAccess(691)->create($this->institution + [
        'ref_cod_tipo_usuario' => LegacyUserTypeFactory::new()->create(['nivel' => App_Model_NivelTipoUsuario::BIBLIOTECA]),
    ])],
]);
