<?php

use App\Events\ActiveLookingChanged;
use App\Models\LegacyActiveLooking;
use App\Process;
use Database\Factories\FileFactory;
use Database\Factories\FileRelationFactory;
use Database\Factories\LegacyActiveLookingFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserTypeFactory;
use iEducar\Modules\School\Model\ActiveLooking;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Tests\Request\Support\EnrollmentScenario;
use Tests\Request\Support\LegacyFields;
use Tests\Request\Support\LegacyForm;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    $this->student = $this->scenario->student('Ana Alves');
    $this->registration = $this->scenario->registration(['ref_cod_aluno' => $this->student, 'data_matricula' => '2030-02-10']);
    $this->scenario->enroll($this->registration, $this->scenario->schoolClass(), ['data_enturmacao' => '2030-02-10']);

    $this->looking = fn (array $attributes = []) => LegacyActiveLookingFactory::new()->create($attributes + [
        'registration_id' => $this->registration,
        'start' => '2030-03-02',
        'end' => '2030-03-20',
        'result' => ActiveLooking::ACTIVE_LOOKING_WITH_ABSENCE_RESULT,
    ]);

    $this->new = '/intranet/educar_busca_ativa_cad.php?ref_cod_matricula=' . $this->registration->getKey();
    $this->edit = fn ($looking) => $this->new . '&id=' . $looking->getKey();
    $this->list = 'educar_busca_ativa_lst.php?ref_cod_matricula=' . $this->registration->getKey();

    $this->payload = fn (array $overrides = [], ?string $url = null) => LegacyFields::from($this->get($url ?? $this->new)->assertOk()->getContent(), $overrides + [
        'data_inicio' => '02/03/2030',
        'data_fim' => '20/03/2030',
        'resultado_busca_ativa' => (string) ActiveLooking::ACTIVE_LOOKING_WITH_ABSENCE_RESULT,
    ]);

    $this->stored = fn () => LegacyActiveLooking::query()->where('ref_cod_matricula', $this->registration->getKey())->sole();
});

test('abre o cadastro vazio, com os resultados possíveis e o campo de observações', function () {
    $html = $this->get($this->new)->assertOk()->assertSee('name="observacoes"', false)->getContent();
    $fields = LegacyFields::from($html);

    expect($fields['tipoacao'])->toBe('Novo')
        ->and($fields['ref_cod_matricula'])->toBe((string) $this->registration->getKey())
        ->and($fields['data_inicio'])->toBe('')
        ->and($fields['data_fim'])->toBe('')
        ->and(array_values(array_filter(LegacyForm::from($html)->options('resultado_busca_ativa'))))->toBe([
            'Deixou de Frequentar',
            'Transferência',
            'Retorno com ausência justificada',
            'Retorno sem ausência justificada',
        ]);
});

test('mostra a sigla do programa de combate à evasão quando configurada', function () {
    $this->get($this->new)->assertOk()->assertSee('Aluno incluso em programa de combate a evasão escolar?');

    config(['legacy.app.busca_ativa.sigla_programa_evasao' => 'APOIA']);

    $this->get($this->new)->assertOk()->assertSee('Aluno incluso em programa de combate a evasão escolar (APOIA)?');
});

test('cadastra uma busca ativa encerrada', function () {
    $this->post($this->new, ($this->payload)())
        ->assertRedirect($this->list)
        ->assertSessionHas('legacy', 'Cadastro efetuado com sucesso.<br />');

    $looking = ($this->stored)();

    expect($looking->data_inicio->format('Y-m-d'))->toBe('2030-03-02')
        ->and($looking->data_fim->format('Y-m-d'))->toBe('2030-03-20')
        ->and($looking->resultado_busca_ativa)->toBe(ActiveLooking::ACTIVE_LOOKING_WITH_ABSENCE_RESULT)
        ->and((bool) $looking->aluno_incluso_programa_evasao)->toBeFalse()
        ->and($looking->messages()->count())->toBe(0);
});

test('grava a observação como mensagem do usuário e a inclusão no programa de evasão', function () {
    $this->post($this->new, ($this->payload)(['observacoes' => 'Família avisada por telefone', 'aluno_incluso_programa_evasao' => 'on']))
        ->assertRedirect($this->list);

    $looking = ($this->stored)();

    expect((bool) $looking->aluno_incluso_programa_evasao)->toBeTrue()
        ->and($looking->messages()->sole())
        ->description->toBe('Família avisada por telefone')
        ->user_id->toBe($this->user->getKey());
});

test('sem data de encerramento a busca fica em andamento, qualquer que seja o resultado enviado', function () {
    $this->post($this->new, ($this->payload)(['data_fim' => '', 'resultado_busca_ativa' => (string) ActiveLooking::ACTIVE_LOOKING_ABANDONMENT_RESULT]))
        ->assertRedirect($this->list);

    expect(($this->stored)())
        ->data_fim->toBeNull()
        ->resultado_busca_ativa->toBe(ActiveLooking::ACTIVE_LOOKING_IN_PROGRESS_RESULT);
});

test('leva para a tela do desfecho quando o resultado é abandono ou transferência', function (int $result, string $screen) {
    $this->post($this->new, ($this->payload)(['resultado_busca_ativa' => (string) $result]))
        ->assertRedirect("{$screen}?ref_cod_matricula={$this->registration->getKey()}&ref_cod_aluno={$this->student->getKey()}");

    expect(($this->stored)()->resultado_busca_ativa)->toBe($result);
})->with([
    'abandono' => [ActiveLooking::ACTIVE_LOOKING_ABANDONMENT_RESULT, 'educar_abandono_cad.php'],
    'transferência' => [ActiveLooking::ACTIVE_LOOKING_TRANSFER_RESULT, 'educar_transferencia_solicitacao_cad.php'],
]);

test('recusa datas incoerentes', function (array $dates, string $message) {
    $this->post($this->new, ($this->payload)($dates))
        ->assertOk()
        ->assertSee($message);

    expect(LegacyActiveLooking::query()->where('ref_cod_matricula', $this->registration->getKey())->exists())->toBeFalse();
})->with([
    'início antes da matrícula' => [['data_inicio' => '05/02/2030'], 'Data de início não pode ser menor que a data matrícula.'],
    'encerramento antes do início' => [['data_fim' => '01/03/2030'], 'Data de retorno não pode ser menor que a data de início'],
]);

test('recusa uma busca ativa no período de outra', function () {
    ($this->looking)();

    $this->post($this->new, ($this->payload)(['data_inicio' => '10/03/2030', 'data_fim' => '30/03/2030']))
        ->assertOk()
        ->assertSee('Já existe uma busca ativa lançada para o período selecionado.');

    expect(LegacyActiveLooking::query()->where('ref_cod_matricula', $this->registration->getKey())->count())->toBe(1);
});

test('mostra a mensagem de uma falha ao gravar e não grava', function () {
    Event::listen(ActiveLookingChanged::class, fn () => throw new RuntimeException('Falha ao avisar a rede de proteção'));

    $this->post($this->new, ($this->payload)())
        ->assertOk()
        ->assertSee('Falha ao avisar a rede de proteção');

    expect(LegacyActiveLooking::query()->where('ref_cod_matricula', $this->registration->getKey())->exists())->toBeFalse();
});

test('abre a edição com os dados gravados, as observações e o campo de nova observação', function () {
    $looking = ($this->looking)(['aluno_incluso_programa_evasao' => true]);
    $looking->messages()->create(['description' => 'Família avisada por telefone', 'user_id' => $this->user->getKey()]);

    $html = $this->get(($this->edit)($looking))
        ->assertOk()
        ->assertSee('Família avisada por telefone')
        ->assertSee('name="nova_observacao"', false)
        ->assertDontSee('name="observacoes"', false)
        ->assertSee("id='btn_excluir'", false)
        ->getContent();

    $fields = LegacyFields::from($html);

    expect($fields['tipoacao'])->toBe('Editar')
        ->and($fields['id'])->toBe((string) $looking->getKey())
        ->and($fields['data_inicio'])->toBe('02/03/2030')
        ->and($fields['data_fim'])->toBe('20/03/2030')
        ->and($fields['resultado_busca_ativa'])->toBe((string) ActiveLooking::ACTIVE_LOOKING_WITH_ABSENCE_RESULT)
        ->and($fields)->toHaveKey('aluno_incluso_programa_evasao');
});

test('edita a busca ativa', function () {
    $looking = ($this->looking)(['end' => null, 'result' => ActiveLooking::ACTIVE_LOOKING_IN_PROGRESS_RESULT]);

    $payload = ($this->payload)([
        'data_fim' => '25/03/2030',
        'resultado_busca_ativa' => (string) ActiveLooking::ACTIVE_LOOKING_WITHOUT_ABSENCE_RESULT,
    ], ($this->edit)($looking));

    $this->post(($this->edit)($looking), $payload)
        ->assertRedirect($this->list)
        ->assertSessionHas('legacy', 'Cadastro atualizado com sucesso.<br />');

    expect($looking->refresh()->data_fim->format('Y-m-d'))->toBe('2030-03-25')
        ->and($looking->resultado_busca_ativa)->toBe(ActiveLooking::ACTIVE_LOOKING_WITHOUT_ABSENCE_RESULT)
        ->and(LegacyActiveLooking::query()->where('ref_cod_matricula', $this->registration->getKey())->count())->toBe(1);
});

test('anexa os arquivos enviados e remove os marcados para exclusão', function () {
    Storage::fake();

    $looking = ($this->looking)();
    $old = FileFactory::new()->create(['original_name' => 'antigo.pdf']);
    FileRelationFactory::new()->create(['relation_type' => LegacyActiveLooking::class, 'relation_id' => $looking->getKey(), 'file_id' => $old->getKey()]);

    $this->get(($this->edit)($looking))->assertOk()->assertSee('antigo.pdf');

    $payload = ($this->payload)([
        'file_url' => json_encode([['url' => 'https://exemplo.test/a/b/c/atestado.pdf', 'size' => 1234, 'originalName' => 'atestado.pdf', 'extension' => 'pdf']]),
        'file_url_deleted' => (string) $old->getKey(),
    ], ($this->edit)($looking));

    $this->post(($this->edit)($looking), $payload)->assertRedirect($this->list);

    expect($looking->files()->pluck('original_name')->all())->toBe(['atestado.pdf'])
        ->and($looking->files()->sole())
        ->url->toBe('https://exemplo.test/a/b/c/atestado.pdf')
        ->size->toBe(1234)
        ->extension->toBe('pdf');

    $this->assertModelMissing($old);
});

test('exclui a busca ativa e os seus arquivos', function () {
    Storage::fake();

    $looking = ($this->looking)();
    $file = FileFactory::new()->create();
    FileRelationFactory::new()->create(['relation_type' => LegacyActiveLooking::class, 'relation_id' => $looking->getKey(), 'file_id' => $file->getKey()]);

    $payload = ($this->payload)(['tipoacao' => 'Excluir'], ($this->edit)($looking));

    $this->post(($this->edit)($looking), $payload)
        ->assertRedirect($this->list)
        ->assertSessionHas('legacy', 'Exclusão efetuada com sucesso.');

    $this->assertSoftDeleted($looking);
    $this->assertModelMissing($file);
});

test('exclui a busca ativa sem arquivos', function () {
    $looking = ($this->looking)();

    $this->post(($this->edit)($looking), ($this->payload)(['tipoacao' => 'Excluir'], ($this->edit)($looking)))->assertRedirect($this->list);

    $this->assertSoftDeleted($looking);
});

test('avisa quando a exclusão falha', function () {
    $looking = ($this->looking)();

    Event::listen('eloquent.deleting: ' . LegacyActiveLooking::class, fn () => throw new RuntimeException('Falha'));

    $this->post(($this->edit)($looking), ($this->payload)(['tipoacao' => 'Excluir'], ($this->edit)($looking)))
        ->assertOk()
        ->assertSee('Exclusão não realizada.');

    $this->assertNotSoftDeleted($looking);
});

test('defeito: excluir sem o código da busca responde sucesso sem excluir nada', function () {
    $looking = ($this->looking)();

    $this->post($this->new, ($this->payload)(['tipoacao' => 'Excluir']))
        ->assertRedirect($this->list)
        ->assertSessionHas('legacy', 'Exclusão efetuada com sucesso.');

    $this->assertNotSoftDeleted($looking);
});

test('defeito: uma data de início inválida estoura a tela', function () {
    $this->post($this->new, ($this->payload)(['data_inicio' => '2030-03-02']))->assertStatus(500);
});

test('quem só pode visualizar não grava nem exclui e não vê o botão de excluir', function (string $action) {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $looking = ($this->looking)(['end' => null, 'result' => ActiveLooking::ACTIVE_LOOKING_IN_PROGRESS_RESULT]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(Process::ACTIVE_LOOKING, modify: false, remove: false)->withAccess(578, modify: false, remove: false)
            ->create(['ref_cod_instituicao' => $this->scenario->institution])
    );

    $payload = ($this->payload)(['tipoacao' => $action, 'data_fim' => '25/03/2030'], ($this->edit)($looking));

    $this->get(($this->edit)($looking))->assertOk()->assertDontSee("id='btn_excluir'", false);

    $this->post(($this->edit)($looking), $payload)->assertRedirect('educar_matricula_det.php?cod_matricula=' . $this->registration->getKey());

    expect($looking->refresh())
        ->data_fim->toBeNull()
        ->deleted_at->toBeNull();
})->with(['Editar', 'Excluir']);

test('volta para a listagem de matrículas sem o código da matrícula ou com uma matrícula que não existe', function (string $query) {
    $this->get('/intranet/educar_busca_ativa_cad.php' . $query)->assertRedirect('educar_matricula_lst.php');
})->with([
    'sem código' => [''],
    'matrícula inexistente' => ['?ref_cod_matricula=999999999'],
]);

test('manda para o início o usuário de biblioteca', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $this->actingAs(LegacyUserFactory::new()->withAccess(Process::ACTIVE_LOOKING)->withAccess(578)->create([
        'ref_cod_instituicao' => $this->scenario->institution,
        'ref_cod_tipo_usuario' => LegacyUserTypeFactory::new()->create(['nivel' => App_Model_NivelTipoUsuario::BIBLIOTECA]),
    ]));

    $this->get($this->new)->assertRedirect('/intranet/index.php');
});
