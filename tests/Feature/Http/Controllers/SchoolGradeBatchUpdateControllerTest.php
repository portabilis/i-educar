<?php

use App\Listeners\LoginLegacySession;
use App\Services\SchoolGradeImportService;
use App\User;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Request\Support\EnrollmentScenario;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    app(LoginLegacySession::class)->handle(new Login('web', User::query()->findOrFail($this->user->getKey()), false));

    $this->scenario = EnrollmentScenario::for($this->user, now()->year);
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);
    $this->second = $this->scenario->grade('Segundo Ano');

    $this->lines = fn () => [
        ['escolas' => [$this->scenario->school->getKey()], 'series' => [$this->scenario->grade->getKey(), $this->second->getKey()]],
        ['escolas' => [], 'series' => [$this->second->getKey()]],
    ];
    $this->completed = ['status' => 'completed', 'message' => 'Processamento concluído.', 'errors' => [], 'details' => []];
});

test('as telas de atualização em lote e de resultado abrem', function () {
    $this->get(route('school-grade.batch-update.index'))
        ->assertOk()
        ->assertViewHas('user');

    $this->withSession(['batch_update_result' => $this->completed])
        ->get(route('school-grade.batch-update.status'))
        ->assertOk()
        ->assertViewHas('result', $this->completed)
        ->assertSessionMissing('batch_update_result');
});

test('a visualização exige ano e escolas com séries', function () {
    $this->getJson(route('school-grade.batch-update.preview', ['ano' => now()->year]))
        ->assertOk()
        ->assertExactJson(['status' => 'error', 'message' => 'Ano e dados de escola/série são obrigatórios.', 'errors' => [['error' => 'Ano e dados de escola/série são obrigatórios.']]]);
});

test('a visualização monta a tabela de escolas por linha com as séries agrupadas por curso', function () {
    $this->getJson(route('school-grade.batch-update.preview', ['ano' => now()->year, 'escola_serie' => ($this->lines)(), 'bloquear_enturmacao_sem_vagas' => 1]))
        ->assertOk()
        ->assertJson(['status' => 'success', 'preview' => [
            'year' => (string) now()->year,
            'total_combinations' => 2,
            'total_lines' => 1,
            'table_data' => [[
                'school' => ['id' => $this->scenario->school->getKey(), 'name' => 'ESCOLA AURORA'],
                'courses' => [['course_name' => 'Ensino Fundamental', 'series' => 'Primeiro Ano<br>Segundo Ano']],
            ]],
            'blocking_params' => ['bloquear_enturmacao_sem_vagas' => 1, 'bloquear_cadastro_turma_para_serie_com_vagas' => 0],
        ]]);
});

test('o processamento valida o ano e as linhas, e responde com erro genérico quando a validação falha', function () {
    $this->postJson(route('school-grade.batch-update.process'), ['ano' => 1999, 'escola_serie' => ($this->lines)()])
        ->assertOk()
        ->assertJson(['status' => 'error', 'message' => 'Erro ao processar atualização. Por favor, tente novamente.']);
});

test('o processamento repassa escolas, séries e bloqueios ao serviço e guarda o resultado na sessão', function () {
    $this->mock(SchoolGradeImportService::class)
        ->shouldReceive('processSchoolGradeUpdate')->once()
        ->withArgs(fn (array $params) => $params['year'] == now()->year
            && $params['schools'] === [$this->scenario->school->getKey()]
            && $params['grades'] === [$this->scenario->grade->getKey(), $this->second->getKey()]
            && count($params['escola_serie_data']) === 1
            && $params['user']->is($this->user)
            && $params['bloquear_enturmacao_sem_vagas'] == 1
            && $params['bloquear_cadastro_turma_para_serie_com_vagas'] == 0)
        ->andReturn($this->completed);

    $this->postJson(route('school-grade.batch-update.process'), ['ano' => now()->year, 'escola_serie' => ($this->lines)(), 'bloquear_enturmacao_sem_vagas' => 1])
        ->assertOk()
        ->assertExactJson(['status' => 'success', 'message' => 'Processamento concluído.', 'redirect' => route('school-grade.batch-update.status')])
        ->assertSessionHas('batch_update_result', $this->completed);
});

test('a falha do serviço e a exceção do serviço voltam como erro', function () {
    $this->mock(SchoolGradeImportService::class, function ($mock) {
        $mock->shouldReceive('processSchoolGradeUpdate')->once()->andReturn(['status' => 'failed', 'message' => 'Nada feito.', 'errors' => [['error' => 'x'], ['error' => 'x']], 'details' => ['d']]);
        $mock->shouldReceive('processSchoolGradeUpdate')->once()->andThrow(new RuntimeException('boom'));
    });

    $data = ['ano' => now()->year, 'escola_serie' => ($this->lines)()];

    $this->postJson(route('school-grade.batch-update.process'), $data)
        ->assertOk()
        ->assertExactJson(['status' => 'error', 'message' => 'Nada feito.', 'errors' => [['error' => 'x']], 'details' => ['d']]);

    $this->postJson(route('school-grade.batch-update.process'), $data)
        ->assertOk()
        ->assertJson(['status' => 'error', 'message' => 'Erro ao processar atualização. Por favor, tente novamente.']);
});

test('a visualização avisa quando não consegue montar a prévia', function () {
    // Código de escola inválido faz a consulta falhar; por isso este é o último passo do teste.
    $this->getJson(route('school-grade.batch-update.preview', ['ano' => now()->year, 'escola_serie' => [1 => ['escolas' => ['abc'], 'series' => ['xyz']]]]))
        ->assertOk()
        ->assertJson(['status' => 'error', 'message' => 'Erro ao processar preview.']);
});
