<?php

use App\Events\NotificationEvent;
use App\Jobs\ComponentBatchOperationJob;
use App\Listeners\LoginLegacySession;
use App\Models\ComponentBatchOperation;
use App\Models\Enums\ComponentBatchStatus;
use App\Models\Notification;
use App\Services\ComponentBatchManagerService;
use App\User;
use Database\Factories\LegacyDisciplineScoreFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyRegistrationScoreFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Testing\Fakes\PendingBatchFake;
use Tests\Request\Support\EnrollmentScenario;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    Event::fake([NotificationEvent::class]);

    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    // O layout lê da sessão o que o listener de login grava.
    app(LoginLegacySession::class)->handle(new Login('web', User::query()->findOrFail($this->user->getKey()), false));

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    $this->school = $this->scenario->school;
    $this->course = $this->scenario->course;
    $this->grade = $this->scenario->grade;
    $this->discipline = $this->scenario->discipline('Matemática');

    LegacyGeneralConfigurationFactory::new()->create([
        'ref_cod_instituicao' => $this->scenario->institution,
        'url_novo_educacao' => null,
        'token_novo_educacao' => null,
    ]);

    // O formulário, com os nomes dos campos do legado e todas as operações;
    // um checkbox desmarcado não vem na requisição, então sai do array.
    $this->form = fn (array $overrides = [], array $without = []) => array_diff_key($overrides + [
        'ano' => 2030,
        'ref_cod_instituicao' => $this->scenario->institution,
        'escola' => [$this->school->getKey()],
        'curso' => [$this->course->getKey()],
        'ref_cod_serie' => [$this->grade->getKey()],
        'discipline_ids' => [$this->discipline->getKey()],
        'remove_records' => 1,
        'unlink_class_components' => 1,
        'unlink_teacher_disciplines' => 1,
        'unlink_school_grade_disciplines' => 1,
        'unlink_grade_components' => 1,
        'remove_exemptions' => 1,
    ], array_flip($without));

    $this->operations = ['remove_records', 'unlink_class_components', 'unlink_teacher_disciplines', 'unlink_school_grade_disciplines', 'unlink_grade_components', 'remove_exemptions'];

    // O que o preview deixa na sessão para a execução.
    $this->validated = fn (array $overrides = []) => $overrides + [
        'year' => 2030,
        'institution_id' => $this->scenario->institution,
        'school_ids' => [$this->school->getKey()],
        'course_ids' => [$this->course->getKey()],
        'grade_ids' => [$this->grade->getKey()],
        'discipline_ids' => [$this->discipline->getKey()],
        'remove_records' => true,
        'unlink_class_components' => true,
        'unlink_teacher_disciplines' => true,
        'unlink_school_grade_disciplines' => true,
        'unlink_grade_components' => true,
        'remove_exemptions' => true,
        'preview_counts' => ['turma_count' => 0, 'idiario' => null, 'escola_serie_disciplina' => 1, 'componente_ano_escolar' => 1],
    ];

    $this->operation = fn (ComponentBatchStatus $status = ComponentBatchStatus::WAITING, array $data = []) => ComponentBatchOperation::query()->create([
        'user_id' => $this->user->getKey(),
        'status_id' => $status->value,
        'data' => $data + ($this->validated)(),
    ]);

    $this->score = function () {
        $registration = $this->scenario->enrolled('Bia Borges', $this->scenario->schoolClass())->registration;

        LegacyDisciplineScoreFactory::new()->create([
            'nota_aluno_id' => LegacyRegistrationScoreFactory::new()->create(['matricula_id' => $registration]),
            'componente_curricular_id' => $this->discipline,
            'etapa' => 1,
        ]);
    };

    $this->preview = fn (array $overrides = [], array $without = []) => $this->post(route('component-batch-manager.preview'), ($this->form)($overrides, $without));
});

test('a listagem mostra as operações com o status, e as paradas há mais de 20 minutos como não concluídas', function () {
    $fresh = ($this->operation)(ComponentBatchStatus::COMPLETED);
    $stale = ($this->operation)(ComponentBatchStatus::RUNNING);
    $stale->forceFill(['created_at' => now()->subMinutes(21)])->save();

    $this->get(route('component-batch-manager.index'))
        ->assertOk()
        ->assertSeeInOrder([
            route('component-batch-manager.show', $fresh), 'Concluído',
            route('component-batch-manager.show', $stale), 'A operação não pôde ser concluída',
        ])
        ->assertSee(route('component-batch-manager.create'));
});

test('sem operações a listagem diz que não há nenhuma', function () {
    $this->get(route('component-batch-manager.index'))
        ->assertOk()
        ->assertSee('Nenhuma operação realizada');
});

test('o formulário abre com o ano corrente', function () {
    $this->get(route('component-batch-manager.create'))
        ->assertOk()
        ->assertSee('Nova Operação em Lote')
        ->assertSee('value="' . now()->year . '"', false);
});

test('voltando do preview, o formulário recupera o que foi preenchido', function () {
    $this->withSession(['batch_operation_preview_data' => ($this->validated)()])
        ->get(route('component-batch-manager.create', ['restore' => 1]))
        ->assertOk()
        ->assertSee('value="2030"', false);
});

test('sem o que restaurar, o formulário volta vazio', function () {
    $this->get(route('component-batch-manager.create', ['restore' => 1]))
        ->assertOk()
        ->assertSee('value="' . now()->year . '"', false);
});

test('os cursos das escolas no ano', function () {
    $this->getJson(route('component-batch-manager.api.courses'))
        ->assertOk()
        ->assertExactJson([]);

    $this->getJson(route('component-batch-manager.api.courses', ['school_ids' => [$this->school->getKey()], 'year' => 2030]))
        ->assertOk()
        ->assertExactJson([$this->course->getKey() => 'Ensino Fundamental']);

    $this->getJson(route('component-batch-manager.api.courses', ['school_ids' => [$this->school->getKey()], 'year' => 2031]))
        ->assertOk()
        ->assertExactJson([]);
});

test('as séries dos cursos nas escolas no ano', function () {
    $this->getJson(route('component-batch-manager.api.grades', ['school_ids' => [$this->school->getKey()]]))
        ->assertOk()
        ->assertExactJson([]);

    $query = ['school_ids' => [$this->school->getKey()], 'course_ids' => [$this->course->getKey()], 'year' => 2030];

    $this->getJson(route('component-batch-manager.api.grades', $query))
        ->assertOk()
        ->assertExactJson([$this->grade->getKey() => 'Primeiro Ano']);

    $this->getJson(route('component-batch-manager.api.grades', ['course_ids' => [$this->course->getKey()]]))
        ->assertOk()
        ->assertExactJson([$this->grade->getKey() => 'Primeiro Ano']);
});

test('os componentes das séries nas escolas no ano', function () {
    $this->getJson(route('component-batch-manager.api.disciplines', ['school_ids' => [$this->school->getKey()]]))
        ->assertOk()
        ->assertExactJson([]);

    $query = ['school_ids' => [$this->school->getKey()], 'grade_ids' => [$this->grade->getKey()], 'year' => 2030];

    $this->getJson(route('component-batch-manager.api.disciplines', $query))
        ->assertOk()
        ->assertExactJson([$this->discipline->getKey() => 'Matemática']);

    $this->getJson(route('component-batch-manager.api.disciplines', ['year' => 2031] + $query))
        ->assertOk()
        ->assertExactJson([]);
});

test('o preview exige os filtros e ao menos uma operação', function () {
    $this->post(route('component-batch-manager.preview'), [])
        ->assertRedirect()
        ->assertSessionHasErrors(['year', 'institution_id', 'course_ids', 'grade_ids', 'school_ids', 'discipline_ids', 'operations']);

    ($this->preview)(['ano' => 1999], $this->operations)
        ->assertSessionHasErrors(['year', 'operations' => 'Selecione ao menos uma operação.']);
});

test('o preview confere a cadeia escola, curso, série e componente', function () {
    $other = $this->scenario->grade('Segundo Ano');

    ($this->preview)(['curso' => [$this->course->getKey() + 1000]])
        ->assertSessionHasErrors(['course_ids' => 'Cursos selecionados não pertencem às escolas informadas.']);

    ($this->preview)(['ref_cod_serie' => [$this->grade->getKey(), $other->getKey() + 1000]])
        ->assertSessionHasErrors(['grade_ids' => 'Séries selecionadas não pertencem aos cursos/escolas informados.']);

    ($this->preview)(['discipline_ids' => [$this->discipline->getKey()], 'ref_cod_serie' => [$other->getKey()]])
        ->assertSessionHasErrors(['discipline_ids' => 'Componentes selecionados não pertencem às séries/escolas informadas.']);
});

test('marcar uma operação marca as que ela depende, e desmarcar uma desmarca as que dependem dela', function () {
    ($this->score)();

    // Só o nível mais alto: tudo abaixo vem junto.
    ($this->preview)([], array_diff($this->operations, ['unlink_grade_components']))
        ->assertOk()
        ->assertViewHas('params', fn ($params) => $params['remove_records'] && $params['unlink_class_components']
            && $params['unlink_teacher_disciplines'] && $params['unlink_school_grade_disciplines']
            && $params['unlink_grade_components'] && $params['remove_exemptions']);

    // Só os vínculos de professor: trazem a remoção de lançamentos e nada acima.
    ($this->preview)([], array_diff($this->operations, ['unlink_teacher_disciplines']))
        ->assertOk()
        ->assertViewHas('params', fn ($params) => $params['remove_records'] && $params['unlink_teacher_disciplines']
            && !$params['unlink_class_components'] && !$params['unlink_school_grade_disciplines'] && !$params['unlink_grade_components'] && !$params['remove_exemptions']);
});

test('o preview mostra os filtros, as contagens e os avisos, e guarda tudo na sessão', function () {
    ($this->score)();

    ($this->preview)([], array_diff($this->operations, ['remove_records']))
        ->assertOk()
        ->assertSee("Escola Aurora ({$this->school->getKey()})")
        ->assertSee("Ensino Fundamental ({$this->course->getKey()})")
        ->assertSee("Primeiro Ano ({$this->grade->getKey()})")
        ->assertSee("Matemática ({$this->discipline->getKey()})")
        ->assertSee('id="execute-form"', false)
        ->assertViewHas('totalIeducar', 1)
        ->assertViewHas('totalIdiario', 0)
        ->assertViewHas('blockingError', null)
        ->assertViewHas('protectionDetails', [])
        ->assertViewHas('warnings', [])
        ->assertSessionHas('batch_operation_preview_data', fn ($data) => $data['year'] === 2030 && $data['preview_counts']['nota'] === 1);
});

test('o preview bloqueia quando não há nada a excluir', function () {
    ($this->preview)([], array_diff($this->operations, ['remove_records']))
        ->assertOk()
        ->assertSee('Nenhum registro encontrado para os filtros selecionados.')
        ->assertDontSee('id="execute-form"', false);
});

test('o preview bloqueia quando há vínculos protegidos, detalhando quem os bloqueia', function () {
    $this->mock(ComponentBatchManagerService::class, function ($mock) {
        $mock->shouldReceive('calculatePreview')->andReturn(['turma_count' => 0, 'idiario' => null, 'escola_serie_disciplina' => 0, 'escola_serie_disciplina_skipped' => 1]);
        $mock->shouldReceive('buildWarnings')->andReturn([]);
        $mock->shouldReceive('getProtectionDetails')->andReturn(['escola_serie_disciplina' => [
            ['componente' => 'Matemática', 'escola' => 'Escola Aurora', 'serie' => 'Primeiro Ano', 'anos_bloqueando' => [2029]],
        ]]);
    });

    ($this->preview)()
        ->assertOk()
        ->assertSee('Não é possível executar a operação.')
        ->assertSeeInOrder(['Matemática', 'Escola Aurora', 'Primeiro Ano', '2029'])
        ->assertDontSee('id="execute-form"', false);
});

test('executar sem o preview na sessão volta ao formulário', function () {
    $this->post(route('component-batch-manager.execute'))
        ->assertRedirect(route('component-batch-manager.create'))
        ->assertSessionHasErrors();

    expect(ComponentBatchOperation::query()->count())->toBe(0);
});

test('executar cria a operação, roda o job e avisa o usuário', function () {
    $this->withSession(['batch_operation_preview_data' => ($this->validated)()])
        ->post(route('component-batch-manager.execute'))
        ->assertRedirect(route('component-batch-manager.show', ComponentBatchOperation::query()->latest('id')->first()))
        ->assertSessionMissing('batch_operation_preview_data');

    $operation = ComponentBatchOperation::query()->latest('id')->first();

    expect($operation->user_id)->toBe($this->user->getKey())
        ->and($operation->data['user_id'])->toBe($this->user->getKey())
        ->and($operation->status())->toBe(ComponentBatchStatus::COMPLETED)
        ->and($operation->data['result_counts']['escola_serie_disciplina'])->toBe(1);

    expect(Notification::query()->where('user_id', $this->user->getKey())->value('text'))
        ->toBe('Remoção de componentes concluída com sucesso.');
});

// Com a fila síncrona o job roda dentro da transação que enfileira o lote, e
// uma falha desfaz o que os callbacks gravaram; aqui o lote é falso e os
// callbacks são chamados à mão.
test('o lote avisa o usuário ao terminar, salvo se ainda aguarda o iDiário, e também quando falha', function () {
    Bus::fake();

    $this->withSession(['batch_operation_preview_data' => ($this->validated)()])
        ->post(route('component-batch-manager.execute'))
        ->assertRedirect();

    $operation = ComponentBatchOperation::query()->latest('id')->first();
    $batch = Bus::batched(fn (PendingBatchFake $batch) => $batch->jobs->first() instanceof ComponentBatchOperationJob)->first();
    $notifications = fn () => Notification::query()->where('user_id', $this->user->getKey())->pluck('text')->all();

    $operation->update(['status_id' => ComponentBatchStatus::RUNNING->value]);
    $batch->thenCallbacks()[0]($batch);
    expect($notifications())->toBe([]);

    $operation->update(['status_id' => ComponentBatchStatus::COMPLETED->value]);
    $batch->thenCallbacks()[0]($batch);
    expect($notifications())->toBe(['Remoção de componentes concluída com sucesso.']);

    $batch->catchCallbacks()[0]($batch, new RuntimeException('boom'));
    expect($notifications())->toBe(['Remoção de componentes concluída com sucesso.', 'Erro na remoção de componentes. Clique para ver detalhes.']);
});

test('o resultado mostra a operação em andamento', function () {
    $operation = ($this->operation)(ComponentBatchStatus::RUNNING);

    $this->get(route('component-batch-manager.show', $operation))
        ->assertOk()
        ->assertSee('Em execução')
        ->assertSee('Processamento em andamento...')
        ->assertSee("Escola Aurora ({$this->school->getKey()})")
        ->assertSee("Matemática ({$this->discipline->getKey()})")
        ->assertViewHas('showVerification', false)
        ->assertViewHas('totalIeducar', 2);
});

test('o resultado mostra a operação parada como não concluída', function () {
    $operation = ($this->operation)(ComponentBatchStatus::WAITING);
    $operation->forceFill(['created_at' => now()->subMinutes(21)])->save();

    $this->get(route('component-batch-manager.show', $operation))
        ->assertOk()
        ->assertSee('A operação não pôde ser concluída.');
});

test('o resultado mostra a falha com a mensagem e, se houver, a verificação', function () {
    $operation = ($this->operation)(ComponentBatchStatus::FAILED);
    $operation->update(['error_message' => 'Ocorreu um erro inesperado durante a execução. Contate o administrador.']);

    $this->get(route('component-batch-manager.show', $operation))
        ->assertOk()
        ->assertSee('A operação falhou.')
        ->assertSee('Ocorreu um erro inesperado durante a execução. Contate o administrador.')
        ->assertViewHas('showVerification', false);

    $operation = ($this->operation)(ComponentBatchStatus::FAILED, ['post_idiario' => ['notas' => ['count' => 1]]]);

    $this->get(route('component-batch-manager.show', $operation))
        ->assertOk()
        ->assertViewHas('showVerification', true);
});

test('o resultado da operação concluída mostra o que sobrou e as observações', function () {
    $operation = ($this->operation)(ComponentBatchStatus::COMPLETED, [
        'school_ids' => [],
        'course_ids' => [],
        'post_counts' => ['turma_count' => 0, 'nota' => 0],
        'verification_warnings' => ['Notas: 1 registros remanescentes após exclusão.'],
    ]);

    $this->get(route('component-batch-manager.show', $operation))
        ->assertOk()
        ->assertSee('Concluído')
        ->assertSee('Todas')
        ->assertSee('Todos')
        ->assertSee('Notas: 1 registros remanescentes após exclusão.')
        ->assertViewHas('showVerification', true)
        ->assertViewHas('totalPostIeducar', 0)
        ->assertViewHas('schoolNames', [])
        ->assertViewHas('courseNames', []);
});
