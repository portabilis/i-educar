<?php

use App\Models\LegacyInstitution;
use App\Models\LegacySchoolClassTeacher;
use App\Services\iDiarioService;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use iEducar\Modules\Educacenso\Model\ModalidadeCurso;
use iEducar\Modules\Servidores\Model\FuncaoExercida;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Tests\Request\Support\EmployeeScenario;
use Tests\Request\Support\EnrollmentScenario;
use Tests\Request\Support\LegacyFields;
use Tests\Request\Support\LegacyForm;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->school = EnrollmentScenario::for($this->user, 2030);
    $this->institution = $this->school->institution;
    $this->ana = EmployeeScenario::named('Ana Alves', $this->institution);
    $this->id = $this->ana->employee->getKey();
    $this->class = $this->school->schoolClass(['nm_turma' => 'Turma Alfa']);
    $this->school->period(1);
    $this->school->period(2);

    $this->keys = "ref_cod_servidor={$this->id}&ref_cod_instituicao={$this->institution}";
    $this->new = "/intranet/educar_servidor_vinculo_turma_cad.php?{$this->keys}";
    $this->edit = fn ($link) => "{$this->new}&id={$link->getKey()}";
    $this->list = "educar_servidor_vinculo_turma_lst.php?{$this->keys}";

    $this->payload = fn (array $overrides = []) => $overrides + [
        'tipoacao' => 'Novo',
        'id' => '',
        'servidor_id' => (string) $this->id,
        'copia' => '0',
        'ano' => '2030',
        'ref_cod_instituicao' => (string) $this->institution,
        'ref_cod_escola' => (string) $this->school->school->getKey(),
        'ref_cod_curso' => (string) $this->school->course->getKey(),
        'ref_cod_serie' => (string) $this->school->grade->getKey(),
        'ref_cod_turma' => (string) $this->class->getKey(),
        'funcao_exercida' => (string) FuncaoExercida::DOCENTE,
        'tipo_vinculo' => '2',
    ];

    $this->links = fn () => LegacySchoolClassTeacher::query()->where('servidor_id', $this->id);
    $this->census = fn () => LegacyInstitution::query()->whereKey($this->institution)->update(['obrigar_campos_censo' => true]);

    $this->viewer = function (bool $modify, bool $remove) {
        LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'active_on_ieducar' => true]);

        $this->actingAs(
            LegacyUserFactory::new()->institutional()->withAccess(635, modify: $modify, remove: $remove)->create(['ref_cod_instituicao' => $this->institution])
        );
    };
});

// A edição recusada por não achar o vínculo sai sem desfazer a transação que abriu.
afterEach(function () {
    while (DB::transactionLevel() > 1) {
        DB::rollBack();
    }
});

test('abre o cadastro no ano corrente, com as funções, os tipos de vínculo e os turnos', function () {
    $html = $this->get($this->new)->assertOk()->getContent();
    $fields = LegacyFields::from($html);
    $form = LegacyForm::from($html);

    expect($fields['tipoacao'])->toBe('Novo')
        ->and($fields['servidor_id'])->toBe((string) $this->id)
        ->and($fields['ano'])->toBe(date('Y'))
        ->and($form->options('funcao_exercida'))->toContain('Docente', 'Instrutor da Educação Profissional')
        ->and($form->options('tipo_vinculo'))->toContain('Contrato CLT')
        ->and(array_values($form->options('turma_turno_id')))->toBe(['Selecione', 'Matutino', 'Vespertino']);
});

test('vincula o professor à turma com os componentes, o turno e as datas', function () {
    $arts = $this->school->discipline('Artes');
    $botany = $this->school->discipline('Botânica');

    $this->post($this->new, ($this->payload)([
        'turma_turno_id' => '1',
        'permite_lancar_faltas_componente' => 'on',
        'componentecurricular' => [(string) $arts->getKey(), (string) $botany->getKey(), ''],
        'data_inicial' => '10/02/2030',
        'data_fim' => '15/12/2030',
    ]))
        ->assertRedirect($this->list)
        ->assertSessionHas('legacy', 'Cadastro efetuado com sucesso.<br>');

    $link = ($this->links)()->sole();

    expect($link)
        ->ano->toBe(2030)
        ->instituicao_id->toBe($this->institution)
        ->turma_id->toBe($this->class->getKey())
        ->funcao_exercida->toBe(FuncaoExercida::DOCENTE)
        ->tipo_vinculo->toBe(2)
        ->turno_id->toBe(1)
        ->permite_lancar_faltas_componente->toBe(1)
        ->leciona_itinerario_tecnico_profissional->toBeNull()
        ->area_itinerario->toBeNull()
        ->and(substr((string) $link->data_inicial, 0, 10))->toBe('2030-02-10')
        ->and(substr((string) $link->data_fim, 0, 10))->toBe('2030-12-15')
        ->and($link->disciplines()->pluck('componente_curricular.id')->sort()->values()->all())->toBe([$arts->getKey(), $botany->getKey()]);
});

test('vincula com o mínimo: sem tipo de vínculo, turno, componentes nem datas', function () {
    $this->post($this->new, ($this->payload)(['tipo_vinculo' => '']))->assertRedirect($this->list);

    expect(($this->links)()->sole())
        ->tipo_vinculo->toBeNull()
        ->turno_id->toBeNull()
        ->permite_lancar_faltas_componente->toBe(0)
        ->data_inicial->toBeNull()
        ->data_fim->toBeNull();
});

test('recusa sem turma, com datas incoerentes ou fora do ano letivo, e o vínculo repetido', function (Closure $overrides, string $message) {
    $this->ana->classLink($this->school->schoolClass(['nm_turma' => 'Turma Beta']));

    $payload = ($this->payload)($overrides->call($this));

    if (array_key_exists('ref_cod_turma', $payload) && $payload['ref_cod_turma'] === null) {
        unset($payload['ref_cod_turma']);
    }

    $this->post($this->new, $payload)->assertOk()->assertSee($message, false);

    expect(($this->links)()->count())->toBe(1);
})->with([
    'sem turma' => [fn () => ['ref_cod_turma' => null], 'É necessário selecionar uma turma'],
    'data inicial depois da final' => [fn () => ['data_inicial' => '15/12/2030', 'data_fim' => '10/02/2030'], 'A data inicial do vínculo não pode ser posterior à data final do vínculo.'],
    'data inicial fora do ano' => [fn () => ['data_inicial' => '10/02/2029'], 'A data inicial do vínculo deve estar dentro do ano letivo 2030.'],
    'data final fora do ano' => [fn () => ['data_fim' => '10/02/2031'], 'A data final do vínculo deve estar dentro do ano letivo 2030.'],
    'vínculo repetido' => [fn () => ['ref_cod_turma' => (string) ($this->links)()->value('turma_id')], 'Já existe um vínculo para o(a) professor(a) nesta turma na escola e ano letivo selecionado.'],
]);

test('com a instituição bloqueando vínculo sem alocação, exige a alocação do servidor na escola no ano', function () {
    LegacyInstitution::query()->whereKey($this->institution)->update(['bloquear_vinculo_professor_sem_alocacao_escola' => true]);

    $this->post($this->new, ($this->payload)())
        ->assertOk()
        ->assertSee('Não é possível cadastrar o vínculo pois o servidor não está alocado na escola selecionada.');

    $this->ana->allocation($this->school->school, ['ano' => 2030]);

    $this->post($this->new, ($this->payload)())->assertRedirect($this->list);
});

test('com os campos do censo obrigatórios, valida a função exercida contra a turma', function (array $class, array $overrides, string $message) {
    ($this->census)();
    $this->class->update($class);

    $this->post($this->new, ($this->payload)($overrides))->assertOk()->assertSee($message, false);

    expect(($this->links)()->exists())->toBeFalse();
})->with([
    'instrutor fora das etapas da educação profissional' => [
        [], ['funcao_exercida' => (string) FuncaoExercida::INSTRUTOR_EDUCACAO_PROFISSIONAL],
        'O campo: <b>Função que exerce na turma</b> pode ser <b>Instrutor da Educação Profissional</b> apenas quando',
    ],
    'turma a distância com docente comum' => [
        ['tipo_mediacao_didatico_pedagogico' => 3], [],
        'O campo: <b>Função que exerce na turma</b> deve ser <b>Docente titular</b> ou <b>Docente tutor</b>',
    ],
    'auxiliar em turma que não é curricular' => [
        ['tipo_atendimento' => '{4}'], ['funcao_exercida' => (string) FuncaoExercida::AUXILIAR_EDUCACIONAL],
        'O campo: <b>Função que exerce na turma</b> não pode ser: <b>Auxiliar/Assistente Educacional</b>',
    ],
    'monitor em turma que não é de atividade complementar' => [
        ['tipo_atendimento' => '{0}'], ['funcao_exercida' => (string) FuncaoExercida::MONITOR_ATIVIDADE_COMPLEMENTAR],
        'O campo: <b>Função que exerce na turma</b> não pode ser: <b> Profissional/Monitor de Atividade Complementar </b>',
    ],
    'docente sem área em turma só de aprofundamento' => [
        ['organizacao_curricular' => '{4}'], [],
        'O campo: <b>Área(s) do itinerário formativo</b> deve ser preenchido quando',
    ],
    'docente sem área nem componente em turma de formação geral e aprofundamento' => [
        ['organizacao_curricular' => '{1,4}'], [],
        'O campo: <b>Área(s) do itinerário formativo</b> deve ser preenchido quando',
    ],
    'área informada por quem não é docente' => [
        ['organizacao_curricular' => '{4}'], ['funcao_exercida' => (string) FuncaoExercida::INTERPRETE_LIBRAS, 'area_itinerario' => ['1']],
        'O campo: <b>Área(s) do itinerário formativo</b> não pode ser preenchido quando o campo: <b>Função que exerce na turma</b> for',
    ],
    'área informada em turma sem aprofundamento' => [
        ['organizacao_curricular' => '{1}'], ['area_itinerario' => ['1']],
        'O campo: <b>Área(s) do itinerário formativo</b> não pode ser preenchido quando o campo: <b>Organização Curricular</b> não for',
    ],
    'docente sem dizer se leciona no itinerário técnico' => [
        ['organizacao_curricular' => '{5}'], [],
        'deve ser obrigatório quando o campo: <b>Função que exerce na turma</b>',
    ],
    'leciona no itinerário técnico sem ser docente' => [
        ['organizacao_curricular' => '{5}'], ['funcao_exercida' => (string) FuncaoExercida::INTERPRETE_LIBRAS, 'leciona_itinerario_tecnico_profissional' => '1'],
        'não pode ser preenchido quando o campo: <b>Função que exerce na turm',
    ],
    'leciona no itinerário técnico em turma que não o tem' => [
        ['organizacao_curricular' => '{1}'], ['leciona_itinerario_tecnico_profissional' => '1'],
        'não pode ser preenchido quando o campo: <b>Organização Curricular</b',
    ],
]);

test('com os campos do censo obrigatórios, grava a área do itinerário e o itinerário técnico válidos', function () {
    ($this->census)();
    $this->class->update(['organizacao_curricular' => '{4,5}']);

    $this->post($this->new, ($this->payload)(['area_itinerario' => ['1', '2'], 'leciona_itinerario_tecnico_profissional' => '1']))
        ->assertRedirect($this->list);

    expect(($this->links)()->sole())
        ->leciona_itinerario_tecnico_profissional->toBe(1)
        ->area_itinerario->toBe('{1,2}');
});

test('abre a edição com os dados do vínculo e, na cópia, como cadastro no ano corrente', function () {
    $link = $this->ana->classLink($this->class, ['funcao_exercida' => 2, 'tipo_vinculo' => 1, 'turno_id' => 2, 'permite_lancar_faltas_componente' => 1]);

    $fields = LegacyFields::from($this->get(($this->edit)($link))->assertOk()->assertSee("id='btn_excluir'", false)->getContent());

    expect($fields['tipoacao'])->toBe('Editar')
        ->and($fields['id'])->toBe((string) $link->getKey())
        ->and($fields['ano'])->toBe('2030')
        ->and($fields['funcao_exercida'])->toBe('2')
        ->and($fields['tipo_vinculo'])->toBe('1')
        ->and($fields['turma_turno_id'])->toBe('2')
        ->and($fields)->toHaveKey('permite_lancar_faltas_componente');

    $copy = LegacyFields::from($this->get(($this->edit)($link) . '&copia=1')->assertOk()->getContent());

    expect($copy['tipoacao'])->toBe('Novo')
        ->and($copy['copia'])->toBe('1')
        ->and($copy['ano'])->toBe(date('Y'))
        ->and($copy['funcao_exercida'])->toBe('2');
});

test('numa turma de EJA o turno noturno também é oferecido', function () {
    $this->school->course->update(['modalidade_curso' => ModalidadeCurso::EJA]);
    $link = $this->ana->classLink($this->class);

    $form = LegacyForm::from($this->get(($this->edit)($link))->assertOk()->getContent());

    expect(array_values($form->options('turma_turno_id')))->toBe(['Selecione', 'Matutino', 'Vespertino', 'Noturno']);
});

test('edita o vínculo e troca os componentes', function () {
    $arts = $this->school->discipline('Artes');
    $botany = $this->school->discipline('Botânica');
    $link = $this->ana->classLink($this->class, disciplines: [$arts]);

    $this->post(($this->edit)($link), ($this->payload)([
        'tipoacao' => 'Editar',
        'id' => (string) $link->getKey(),
        'funcao_exercida' => (string) FuncaoExercida::AUXILIAR_EDUCACIONAL,
        'tipo_vinculo' => '4',
        'componentecurricular' => [(string) $botany->getKey()],
        'data_inicial' => '10/02/2030',
    ]))
        ->assertRedirect($this->list)
        ->assertSessionHas('legacy', 'Edição efetuada com sucesso.<br>');

    expect($link->refresh())
        ->funcao_exercida->toBe(FuncaoExercida::AUXILIAR_EDUCACIONAL)
        ->tipo_vinculo->toBe(4)
        ->and(substr((string) $link->data_inicial, 0, 10))->toBe('2030-02-10')
        ->and($link->disciplines()->pluck('componente_curricular.id')->all())->toBe([$botany->getKey()]);
});

test('recusa a edição que repete outro vínculo, com datas fora do ano ou de um vínculo que não existe', function (Closure $overrides, string $message) {
    $link = $this->ana->classLink($this->class);
    $other = $this->ana->classLink($this->school->schoolClass(['nm_turma' => 'Turma Beta']));

    $this->post(($this->edit)($link), ($this->payload)($overrides->call($this, $other) + ['tipoacao' => 'Editar', 'id' => (string) $link->getKey()]))
        ->assertOk()
        ->assertSee($message, false);

    expect($link->refresh()->turma_id)->toBe($this->class->getKey());
})->with([
    'outro vínculo na mesma turma' => [fn ($other) => ['ref_cod_turma' => (string) $other->turma_id], 'Já existe um vínculo para o(a) professor(a) nesta turma na escola e ano letivo selecionado.'],
    'data final fora do ano' => [fn () => ['data_fim' => '10/02/2031'], 'A data final do vínculo deve estar dentro do ano letivo 2030.'],
    'vínculo inexistente' => [fn () => ['id' => '999999999', 'ref_cod_turma' => (string) $this->school->schoolClass()->getKey()], 'Edição não realizada.'],
]);

test('na edição valem as mesmas regras do censo e da alocação na escola', function (array $institution, array $class, string $message) {
    $link = $this->ana->classLink($this->class);

    LegacyInstitution::query()->whereKey($this->institution)->update($institution);
    $this->class->update($class);

    $this->post(($this->edit)($link), ($this->payload)(['tipoacao' => 'Editar', 'id' => (string) $link->getKey(), 'tipo_vinculo' => '4']))
        ->assertOk()
        ->assertSee($message, false);

    expect($link->refresh()->tipo_vinculo)->toBe(2);
})->with([
    'censo' => [['obrigar_campos_censo' => true], ['tipo_mediacao_didatico_pedagogico' => 3], 'deve ser <b>Docente titular</b> ou <b>Docente tutor</b>'],
    'alocação' => [['bloquear_vinculo_professor_sem_alocacao_escola' => true], [], 'Não é possível cadastrar o vínculo pois o servidor não está alocado na escola selecionada.'],
]);

test('com lançamentos do professor no i-Diário para a turma, o turno fica travado na edição', function (bool $activity) {
    $link = $this->ana->classLink($this->class);

    $this->mock(iDiarioService::class, fn ($mock) => $mock->shouldReceive('getTeacherClassroomsActivity')->with($this->id, $this->class->getKey())->andReturn($activity));

    $html = $this->get(($this->edit)($link))->assertOk()->getContent();

    preg_match('/<select[^>]*name=.turma_turno_id.[^>]*>/', $html, $select);

    expect(str_contains($select[0], 'disabled'))->toBe($activity);
})->with(['com lançamentos' => [true], 'sem lançamentos' => [false]]);

test('mostra a mensagem de uma falha ao gravar, editar ou excluir, sem alterar o vínculo', function (string $action, string $event) {
    $link = $this->ana->classLink($this->class);
    $other = $this->school->schoolClass(['nm_turma' => 'Turma Beta']);

    Event::listen("eloquent.{$event}: " . LegacySchoolClassTeacher::class, fn () => throw new RuntimeException('Falha ao gravar o vínculo'));

    $payload = $action === 'Novo'
        ? ($this->payload)(['ref_cod_turma' => (string) $other->getKey()])
        : ($this->payload)(['tipoacao' => $action, 'id' => (string) $link->getKey(), 'tipo_vinculo' => '4']);

    $this->post(($this->edit)($link), $payload)->assertOk()->assertSee('Falha ao gravar o vínculo');

    expect(($this->links)()->count())->toBe(1)
        ->and($link->refresh()->tipo_vinculo)->toBe(2);
})->with([
    ['Novo', 'creating'],
]);

test('exclui o vínculo e os seus componentes', function () {
    $link = $this->ana->classLink($this->class, disciplines: [$this->school->discipline('Artes')]);

    $this->post(($this->edit)($link), ($this->payload)(['tipoacao' => 'Excluir', 'id' => (string) $link->getKey()]))
        ->assertRedirect($this->list)
        ->assertSessionHas('legacy', 'Exclusão efetuada com sucesso.<br>');

    expect(LegacySchoolClassTeacher::query()->whereKey($link->getKey())->exists())->toBeFalse();

    $this->assertDatabaseMissing('modules.professor_turma_disciplina', ['professor_turma_id' => $link->getKey()]);
});

test('sem o código, a exclusão volta para a listagem geral', function () {
    $this->post($this->new, ($this->payload)(['tipoacao' => 'Excluir']))->assertRedirect(url('/intranet/educar_servidor_vinculo_turma_lst.php'));
});

test('quem só pode visualizar não abre, não grava e não exclui', function (string $action) {
    $link = $this->ana->classLink($this->class);

    ($this->viewer)(false, false);

    $this->get($this->new)->assertRedirect($this->list);
    $this->post(($this->edit)($link), ($this->payload)(['tipoacao' => $action, 'id' => (string) $link->getKey(), 'tipo_vinculo' => '4', 'ref_cod_turma' => (string) $this->school->schoolClass()->getKey()]))
        ->assertRedirect($this->list);

    expect(($this->links)()->count())->toBe(1)
        ->and($link->refresh()->tipo_vinculo)->toBe(2);
})->with(['Novo', 'Editar', 'Excluir']);

test('quem não pode remover não vê o botão de excluir', function () {
    $link = $this->ana->classLink($this->class);

    ($this->viewer)(true, false);

    $this->get(($this->edit)($link))->assertOk()->assertDontSee("id='btn_excluir'", false);
});

test('com o censo obrigatório, uma turma que não existe não é vinculada', function () {
    ($this->census)();

    $this->post($this->new, ($this->payload)(['ref_cod_turma' => '999999999']))->assertOk();

    expect(($this->links)()->count())->toBe(0);
});

test('um ano que não é número não é vinculado', function () {
    $this->post($this->new, ($this->payload)(['ano' => 'abc']))->assertOk();

    expect(($this->links)()->count())->toBe(0);
});
