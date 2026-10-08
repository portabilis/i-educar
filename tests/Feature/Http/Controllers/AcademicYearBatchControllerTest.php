<?php

use App\Exceptions\AcademicYearServiceException;
use App\Listeners\LoginLegacySession;
use App\Process;
use App\Services\AcademicYearService;
use App\User;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Request\Support\EnrollmentScenario;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->login = function ($user) {
        $this->user = $user;
        $this->actingAs($user);
        app(LoginLegacySession::class)->handle(new Login('web', User::query()->findOrFail($user->getKey()), false));
    };
    ($this->login)(LegacyUserFactory::new()->admin()->create());

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    // O ano não pode passar de dois anos à frente.
    $this->year = now()->year + 1;
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);
    $this->stageType = DB::table('pmieducar.modulo')->where('ref_cod_instituicao', $this->scenario->institution)->value('cod_modulo');

    $this->create = fn (array $overrides = []) => $overrides + [
        'acao' => AcademicYearService::ACTION_CREATE,
        'ano' => $this->year,
        'ref_cod_instituicao' => $this->scenario->institution,
        'escola' => [[$this->scenario->school->getKey(), ''], null],
        'ref_cod_modulo' => $this->stageType,
        'periodos' => [
            ['data_inicio' => "01/02/{$this->year}", 'data_fim' => "30/06/{$this->year}", 'dias_letivos' => 100],
            ['data_inicio' => '', 'data_fim' => '', 'dias_letivos' => ''],
        ],
    ];

    $this->completed = ['status' => 'completed', 'message' => 'Processamento concluído. 1 escola(s) processada(s).', 'errors' => [], 'details' => [['type' => 'success', 'message' => 'ok']]];
    $this->process = fn (array $data) => $this->postJson(route('academic-year.process'), $data);
});

test('a tela abre para administradores e barra os demais', function () {
    $this->get(route('academic-year.edit'))
        ->assertOk()
        ->assertViewIs('academic-year.edit');

    ($this->login)(LegacyUserFactory::new()->institutional()->create());

    $this->from('/intranet/educar_configuracoes_index.php')
        ->get(route('academic-year.edit'))
        ->assertRedirect('/intranet/educar_configuracoes_index.php')
        ->assertSessionHasErrors(['Error' => 'Você não tem permissão para acessar este recurso']);

    // Mesmo com o processo liberado, a tela é só dos administradores.
    ($this->login)(LegacyUserFactory::new()->institutional()->withAccess(Process::ACADEMIC_YEAR_IMPORT)->create());

    $this->from('/intranet/educar_configuracoes_index.php')
        ->get(route('academic-year.edit'))
        ->assertRedirect('/intranet/educar_configuracoes_index.php')
        ->assertSessionHasErrors(['Error' => 'Você não tem permissão para acessar este recurso']);
});

test('o resultado mostra o que ficou na sessão e o esquece', function () {
    $this->withSession(['academic_year_batch_result' => $this->completed])
        ->get(route('academic-year.status'))
        ->assertOk()
        ->assertViewHas('result', $this->completed)
        ->assertSessionMissing('academic_year_batch_result');
});

test('a ação precisa ser válida', function () {
    ($this->process)(['acao' => 'x'])
        ->assertOk()
        ->assertJson(['status' => 'error', 'message' => 'Erro de validação dos campos'])
        ->assertJsonFragment(['error' => 'O campo ação selecionado é inválido.']);
});

test('criar exige ano, instituição, escolas, módulo e períodos', function () {
    ($this->process)(['acao' => AcademicYearService::ACTION_CREATE, 'escola' => [[999999999]]])
        ->assertOk()
        ->assertJson(['status' => 'error'])
        ->assertJsonFragment(['error' => 'O campo ano é obrigatório.'])
        ->assertJsonFragment(['error' => 'O campo módulo é obrigatório.'])
        ->assertJsonFragment(['error' => 'O campo períodos é obrigatório.']);
});

// A exceção de consistência é montada com um validador vazio, então as
// mensagens dos períodos não chegam à resposta.
test('cada período precisa das duas datas', function () {
    ($this->process)(($this->create)(['periodos' => [
        ['data_inicio' => "01/02/{$this->year}", 'data_fim' => '', 'dias_letivos' => ''],
        ['data_inicio' => '', 'data_fim' => "30/06/{$this->year}", 'dias_letivos' => ''],
    ]]))
        ->assertOk()
        ->assertExactJson(['status' => 'error', 'message' => 'Erro de validação dos campos', 'errors' => []]);
});

test('criar limpa as escolas e os períodos vazios e repassa as opções de cópia ao serviço', function () {
    $this->mock(AcademicYearService::class)
        ->shouldReceive('createAcademicYearBatch')->once()
        ->withArgs(fn (array $params) => $params['year'] === $this->year
            && $params['schools'] === [$this->scenario->school->getKey()]
            && count($params['periodos']) === 1
            && $params['moduleId'] === $this->stageType
            && $params['user']->is($this->user)
            && $params['copySchoolClasses'] === true && $params['copyTeacherData'] === true && $params['copyEmployeeData'] === false)
        ->andReturn($this->completed);

    ($this->process)(($this->create)(['copiar_turmas' => 1, 'copiar_alocacoes_e_vinculos_professores' => 'true']))
        ->assertOk()
        ->assertExactJson(['status' => 'success', 'message' => $this->completed['message'], 'redirect' => route('academic-year.status')])
        ->assertSessionHas('academic_year_batch_result', $this->completed);
});

test('quem não é administrador sempre copia as turmas', function () {
    ($this->login)(LegacyUserFactory::new()->institutional()->withAccess(Process::ACADEMIC_YEAR_IMPORT)->create());
    $this->mock(AcademicYearService::class)
        ->shouldReceive('createAcademicYearBatch')->once()
        ->withArgs(fn (array $params) => $params['copySchoolClasses'] === true && $params['copyTeacherData'] === false)
        ->andReturn($this->completed);

    ($this->process)(($this->create)())->assertOk();
});

test('a falha do serviço volta com os erros sem repetir e os detalhes', function () {
    $this->mock(AcademicYearService::class)->shouldReceive('createAcademicYearBatch')->andReturn([
        'status' => 'failed',
        'message' => 'Processamento cancelado devido a erros de validação.',
        'errors' => [['error' => 'Escola X: já existe'], ['error' => 'Escola X: já existe']],
        'details' => [],
    ]);

    ($this->process)(($this->create)())
        ->assertOk()
        ->assertExactJson(['status' => 'error', 'message' => 'Processamento cancelado devido a erros de validação.', 'errors' => [['error' => 'Escola X: já existe']], 'details' => []])
        ->assertSessionMissing('academic_year_batch_result');
});

test('a exceção do serviço vira erro de validação', function () {
    $this->mock(AcademicYearService::class)->shouldReceive('createAcademicYearBatch')->andThrow(new AcademicYearServiceException('Datas em conflito.'));

    ($this->process)(($this->create)())
        ->assertOk()
        ->assertExactJson(['status' => 'error', 'message' => 'Erro de validação', 'errors' => [['error' => 'Datas em conflito.']]]);
});

test('abrir e fechar o ano letivo passam as escolas ao serviço', function (string $action, string $method) {
    $this->mock(AcademicYearService::class)
        ->shouldReceive($method)->once()
        ->withArgs(fn (array $params) => $params['year'] === $this->year && $params['schools'] === [$this->scenario->school->getKey()] && $params['user']->is($this->user))
        ->andReturn($this->completed);

    ($this->process)(['acao' => $action, 'ano' => $this->year, 'ref_cod_instituicao' => $this->scenario->institution, 'escola' => [$this->scenario->school->getKey()]])
        ->assertOk()
        ->assertJson(['status' => 'success']);

    ($this->process)(['acao' => $action])
        ->assertOk()
        ->assertJsonFragment(['error' => 'O campo escola é obrigatório.']);
})->with([
    'abrir' => [AcademicYearService::ACTION_OPEN, 'openAcademicYearBatch'],
    'fechar' => [AcademicYearService::ACTION_CLOSE, 'closeAcademicYearBatch'],
]);
