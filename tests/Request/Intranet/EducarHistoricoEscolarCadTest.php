<?php

use App\Models\LegacyInstitution;
use App\Models\LegacySchoolHistory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacySchoolHistoryDisciplineFactory;
use Database\Factories\LegacySchoolHistoryFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Support\Facades\DB;
use Tests\Request\Support\EnrollmentScenario;
use Tests\Request\Support\LegacyFields;
use Tests\Request\Support\LegacyForm;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    $this->student = $this->scenario->student('Ana Alves');
    LegacyInstitution::query()->whereKey($this->scenario->institution)->update(['cidade' => 'Cidade Sede', 'ref_sigla_uf' => 'SC']);

    $this->history = fn (array $attributes = []) => LegacySchoolHistoryFactory::new()->create($attributes + [
        'ref_cod_aluno' => $this->student,
        'ref_cod_instituicao' => $this->scenario->institution,
        'ref_cod_escola' => null,
        'sequencial' => 1,
        'ano' => 2028,
        'escola' => 'Escola Antiga',
        'escola_cidade' => 'Içara',
        'escola_uf' => 'SC',
        'nm_curso' => 'Curso Antigo',
        'nm_serie' => 'Série Antiga',
        'carga_horaria' => 800,
        'dias_letivos' => 200,
        'frequencia' => 97,
        'faltas_globalizadas' => 12,
        'extra_curricular' => 0,
        'aceleracao' => 0,
        'origem' => 1,
        'aprovado' => 1,
        'posicao' => null,
        'dependencia' => false,
    ]);

    $this->discipline = fn ($history, int $sequential, string $name) => LegacySchoolHistoryDisciplineFactory::new()->create([
        'historico_escolar_id' => $history->id,
        'ref_ref_cod_aluno' => $history->ref_cod_aluno,
        'ref_sequencial' => $history->sequencial,
        'sequencial' => $sequential,
        'nm_disciplina' => $name,
        'nota' => '8.5',
        'faltas' => 3,
        'carga_horaria_disciplina' => 80,
    ]);

    $this->new = '/intranet/educar_historico_escolar_cad.php?ref_cod_aluno=' . $this->student->getKey();
    $this->edit = fn ($history) => $this->new . '&sequencial=' . $history->sequencial;
    $this->list = 'educar_historico_escolar_lst.php?ref_cod_aluno=' . $this->student->getKey();

    // O que o formulário envia; as disciplinas vão em listas paralelas, como a
    // tabela dinâmica da tela as monta.
    $this->payload = fn (array $overrides = []) => $overrides + [
        'tipoacao' => 'Novo',
        'ref_cod_aluno' => (string) $this->student->getKey(),
        'ref_cod_instituicao' => (string) $this->scenario->institution,
        'escola' => 'Colégio Estrela',
        'escola_cidade' => 'Criciúma',
        'escola_uf' => 'SC',
        'nm_curso' => 'Fundamental',
        'nm_serie' => 'Quinto Ano',
        'ano' => '2029',
        'carga_horaria' => '800',
        'cb_faltas_globalizadas' => 'on',
        'faltas_globalizadas' => '9',
        'dias_letivos' => '200',
        'frequencia' => '97,50',
        'observacao' => 'Veio de outra rede',
        'aprovado' => '1',
        'registro' => 'R-1',
        'livro' => 'L-2',
        'folha' => 'F-3',
        'nm_disciplina' => ['Matemática', 'Artes'],
        'tipo_base' => ['1', '2'],
        'nota' => ['8.5', 'A'],
        'faltas' => ['3', ''],
        'carga_horaria_disciplina' => ['80', ''],
        'ordenamento' => ['1', ''],
        'disciplinaDependencia' => [1 => 'on'],
    ];

    $this->stored = fn (int $sequential) => LegacySchoolHistory::query()
        ->where('ref_cod_aluno', $this->student->getKey())
        ->where('sequencial', $sequential)
        ->sole();
});

test('abre o cadastro com o aluno e as situações possíveis', function () {
    $html = $this->get($this->new)->assertOk()->assertSee('Ana Alves')->assertDontSee('name="posicao"', false)->getContent();
    $fields = LegacyFields::from($html);
    $form = LegacyForm::from($html);

    expect($fields['tipoacao'])->toBe('Novo')
        ->and(array_values($form->options('aprovado')))->toBe([
            'Selecione', 'Aprovado', 'Reprovado', 'Cursando', 'Transferido', 'Reclassificado', 'Deixou de Frequentar',
            'Aprovado com dependência', 'Aprovado pelo conselho', 'Reprovado por faltas',
        ]);
});

test('oferece as grades de curso ativas e grava a escolhida', function () {
    $active = DB::table('pmieducar.historico_grade_curso')->insertGetId(['descricao_etapa' => 'Grade Alfa', 'created_at' => now(), 'ativo' => 1]);
    DB::table('pmieducar.historico_grade_curso')->insert(['descricao_etapa' => 'Grade Extinta', 'created_at' => now(), 'ativo' => 0]);

    $options = LegacyForm::from($this->get($this->new)->assertOk()->getContent())->options('historico_grade_curso_id');

    expect($options)->toContain('Grade Alfa')->not->toContain('Grade Extinta');

    $this->post($this->new, ($this->payload)(['historico_grade_curso_id' => (string) $active]))->assertRedirect($this->list);

    expect(($this->stored)(1)->historico_grade_curso_id)->toBe($active);
});

test('mostra o campo de posição quando a instituição controla a posição dos históricos', function () {
    LegacyInstitution::query()->whereKey($this->scenario->institution)->update(['controlar_posicao_historicos' => true]);

    $this->get($this->new)->assertOk()->assertSee('name="posicao"', false);
});

test('cadastra um histórico com as suas disciplinas', function () {
    ($this->history)();

    $this->post($this->new, ($this->payload)(['posicao' => '2', 'dependencia' => 'on', 'aceleracao' => 'on']))
        ->assertRedirect($this->list)
        ->assertSessionHas('legacy', 'Cadastro efetuado com sucesso.<br>');

    $history = ($this->stored)(2);

    expect($history)
        ->escola->toBe('COLÉGIO ESTRELA')
        ->escola_cidade->toBe('CRICIÚMA')
        ->escola_uf->toBe('SC')
        ->nm_curso->toBe('Fundamental')
        ->nm_serie->toBe('Quinto Ano')
        ->ano->toBe(2029)
        ->faltas_globalizadas->toBe(9)
        ->dias_letivos->toBe(200)
        ->observacao->toBe('Veio de outra rede')
        ->aprovado->toBe(1)
        ->ativo->toBe(1)
        ->origem->toBe(1)
        ->extra_curricular->toBe(0)
        ->aceleracao->toBe(1)
        ->posicao->toBe(2)
        ->registro->toBe('R-1')
        ->livro->toBe('L-2')
        ->folha->toBe('F-3')
        ->ref_cod_escola->toBeNull()
        ->ref_cod_instituicao->toBe($this->scenario->institution)
        ->ref_usuario_cad->toBe($this->user->getKey())
        ->and((float) $history->frequencia)->toBe(97.5)
        ->and((float) $history->carga_horaria)->toBe(800.0)
        ->and((bool) $history->dependencia)->toBeTrue();

    $disciplines = $history->disciplines()->orderBy('sequencial')->get();

    expect($disciplines)->toHaveCount(2)
        ->and($disciplines[0])
        ->sequencial->toBe(1)
        ->nm_disciplina->toBe('Matemática')
        ->nota->toBe('8.5')
        ->faltas->toBe(3)
        ->carga_horaria_disciplina->toBe(80)
        ->ordenamento->toBe(1)
        ->tipo_base->toBe(1)
        ->and((bool) $disciplines[0]->dependencia)->toBeFalse()
        ->and($disciplines[1])
        ->sequencial->toBe(2)
        ->nm_disciplina->toBe('Artes')
        ->nota->toBe('A')
        ->faltas->toBeNull()
        ->carga_horaria_disciplina->toBeNull()
        ->ordenamento->toBeNull()
        ->tipo_base->toBe(2)
        ->and((bool) $disciplines[1]->dependencia)->toBeTrue();
});

test('com a escola da rede grava o nome dela, e sem cidade e estado usa os da instituição', function () {
    $this->post($this->new, ($this->payload)([
        'ref_cod_escola' => (string) $this->scenario->school->getKey(),
        'escola' => '',
        'escola_cidade' => '',
        'escola_uf' => '',
        'ref_cod_instituicao' => '',
        'faltas_globalizadas' => '',
        'carga_horaria' => '',
        'dias_letivos' => '',
        'frequencia' => '88.25',
        'extra_curricular' => 'on',
        'historico_grade_curso_id' => '',
        'nm_disciplina' => null,
    ]))->assertRedirect($this->list);

    $history = ($this->stored)(1);

    expect($history)
        ->escola->toBe('ESCOLA AURORA')
        ->escola_cidade->toBe('CIDADE SEDE')
        ->escola_uf->toBe('SC')
        ->ref_cod_escola->toBe($this->scenario->school->getKey())
        ->ref_cod_instituicao->toBe($this->scenario->institution)
        ->faltas_globalizadas->toBeNull()
        ->carga_horaria->toBeNull()
        ->dias_letivos->toBeNull()
        ->extra_curricular->toBe(1)
        ->historico_grade_curso_id->toBeNull()
        ->and((float) $history->frequencia)->toBe(88.25)
        ->and($history->disciplines()->count())->toBe(0);
});

test('abre a edição com os dados e as disciplinas do histórico', function () {
    $history = ($this->history)(['posicao' => 3]);
    ($this->discipline)($history, 1, 'Matemática');
    ($this->discipline)($history, 2, 'Artes');

    $html = $this->get(($this->edit)($history))
        ->assertOk()
        ->assertSee("id='btn_excluir'", false)
        ->assertSeeInOrder(['Artes', 'Matemática'])
        ->getContent();

    $fields = LegacyFields::from($html);

    expect($fields['tipoacao'])->toBe('Editar')
        ->and($fields['sequencial'])->toBe('1')
        ->and($fields['escola'])->toBe('ESCOLA ANTIGA')
        ->and($fields['nm_serie'])->toBe('Série Antiga')
        ->and($fields['ano'])->toBe('2028')
        ->and($fields['faltas_globalizadas'])->toBe('12')
        ->and($fields)->toHaveKey('cb_faltas_globalizadas')
        ->and($fields['aprovado'])->toBe('1');
});

test('edita o histórico e troca as disciplinas', function () {
    $history = ($this->history)();
    ($this->discipline)($history, 1, 'Ciências');

    $this->post(($this->edit)($history), ($this->payload)(['tipoacao' => 'Editar', 'sequencial' => '1']))
        ->assertRedirect($this->list)
        ->assertSessionHas('legacy', 'Edição efetuada com sucesso.<br>');

    expect($history->refresh())
        ->escola->toBe('COLÉGIO ESTRELA')
        ->nm_serie->toBe('Quinto Ano')
        ->ano->toBe(2029)
        ->faltas_globalizadas->toBe(9)
        ->ref_usuario_exc->toBe($this->user->getKey())
        ->and($history->disciplines()->orderBy('sequencial')->pluck('nm_disciplina')->all())->toBe(['Matemática', 'Artes'])
        ->and(LegacySchoolHistory::query()->where('ref_cod_aluno', $this->student->getKey())->count())->toBe(1);
});

test('na edição as faltas globalizadas só valem com a marcação, e sem disciplinas enviadas as antigas ficam', function () {
    $history = ($this->history)();
    ($this->discipline)($history, 1, 'Ciências');

    $this->post(($this->edit)($history), ($this->payload)([
        'tipoacao' => 'Editar',
        'sequencial' => '1',
        'cb_faltas_globalizadas' => null,
        'nm_disciplina' => null,
    ]))->assertRedirect($this->list);

    expect($history->refresh()->faltas_globalizadas)->toBeNull()
        ->and($history->disciplines()->pluck('nm_disciplina')->all())->toBe(['Ciências']);
});

test('defeito: a edição grava a data de exclusão num histórico que continua ativo', function () {
    $history = ($this->history)();

    $this->post(($this->edit)($history), ($this->payload)(['tipoacao' => 'Editar', 'sequencial' => '1']))->assertRedirect($this->list);

    expect($history->refresh())
        ->ativo->toBe(1)
        ->data_exclusao->not->toBeNull();
});

test('a cópia abre os dados do histórico como um cadastro novo e grava outro histórico', function () {
    $history = ($this->history)();
    ($this->discipline)($history, 1, 'Ciências');

    $html = $this->get(($this->edit)($history) . '&copia=true')
        ->assertOk()
        ->assertSee('Ciências')
        ->assertDontSee("id='btn_excluir'", false)
        ->getContent();

    $fields = LegacyFields::from($html);

    expect($fields['tipoacao'])->toBe('Novo')
        ->and($fields['nm_serie'])->toBe('Série Antiga');

    $this->post(($this->edit)($history) . '&copia=true', ($this->payload)(['sequencial' => '1']))->assertRedirect($this->list);

    expect(($this->stored)(2)->nm_serie)->toBe('Quinto Ano')
        ->and($history->refresh()->nm_serie)->toBe('Série Antiga');
});

test('não abre a edição de um histórico interno', function () {
    $history = ($this->history)(['origem' => 0]);

    $this->get(($this->edit)($history))->assertRedirect($this->list);
});

test('exclui o histórico desativando o registro e apagando as disciplinas', function () {
    $history = ($this->history)();
    ($this->discipline)($history, 1, 'Ciências');

    $this->post(($this->edit)($history), ['tipoacao' => 'Excluir', 'ref_cod_aluno' => $this->student->getKey(), 'sequencial' => '1'])
        ->assertRedirect($this->list)
        ->assertSessionHas('legacy', 'Exclusão efetuada com sucesso.<br>');

    expect($history->refresh())
        ->ativo->toBe(0)
        ->ref_usuario_exc->toBe($this->user->getKey())
        ->and($history->disciplines()->count())->toBe(0);
});

test('responde 404 ao editar ou excluir um histórico que não existe', function (string $action) {
    $this->post($this->new, ($this->payload)(['tipoacao' => $action, 'sequencial' => '99']))->assertNotFound();
})->with(['Editar', 'Excluir']);

test('reaberto por um envio sem ação, o formulário mantém o que foi digitado', function () {
    $html = $this->post($this->new, ['nm_serie' => 'Quinto Ano', 'ano' => '2029'])->assertOk()->getContent();

    expect(LegacyFields::from($html))
        ->nm_serie->toBe('Quinto Ano')
        ->ano->toBe('2029');
});

test('o usuário institucional grava na sua instituição', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $user = LegacyUserFactory::new()->institutional()->withAccess(578)->create(['ref_cod_instituicao' => $this->scenario->institution]);
    $this->actingAs($user);

    $this->get($this->new)->assertOk()->assertSee('Ana Alves');

    $this->post($this->new, ($this->payload)())->assertRedirect($this->list);

    expect(($this->stored)(1))
        ->ref_cod_instituicao->toBe($this->scenario->institution)
        ->ref_usuario_cad->toBe($user->getKey());
});

test('quem só pode visualizar não abre, não grava e não exclui', function (string $action) {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $history = ($this->history)();

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(578, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->scenario->institution])
    );

    $this->get(($this->edit)($history))->assertRedirect($this->list);
    $this->post(($this->edit)($history), ($this->payload)(['tipoacao' => $action, 'sequencial' => '1']))->assertRedirect($this->list);

    expect($history->refresh())
        ->ativo->toBe(1)
        ->nm_serie->toBe('Série Antiga')
        ->and(LegacySchoolHistory::query()->where('ref_cod_aluno', $this->student->getKey())->count())->toBe(1);
})->with(['Novo', 'Editar', 'Excluir']);

test('não oferece excluir a quem não pode remover', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $history = ($this->history)();

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(578, remove: false)->create(['ref_cod_instituicao' => $this->scenario->institution])
    );

    $this->get(($this->edit)($history))->assertOk()->assertDontSee("id='btn_excluir'", false);
});
