<?php

use App\Models\LegacyGrade;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyEvaluationRuleFactory;
use Database\Factories\LegacyEvaluationRuleGradeYearFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Support\Facades\DB;
use Tests\Request\Support\LegacyFields;
use Tests\Request\Support\LegacyForm;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->institution = ['ref_cod_instituicao' => $this->user->ref_cod_instituicao];
    $this->course = LegacyCourseFactory::new()->create($this->institution + ['nm_curso' => 'Curso Alfa', 'qtd_etapas' => 4]);

    $this->rule = LegacyEvaluationRuleFactory::new()->current();
    $this->otherRule = LegacyEvaluationRuleFactory::new()->create(['nome' => 'Regra Diferenciada']);

    // Os campos obrigatórios de uma série, do jeito que o formulário os envia.
    // A tabela de regras vai em três listas paralelas, uma linha por ano.
    $this->required = fn () => $this->institution + [
        'ref_cod_curso' => $this->course->getKey(),
        'nm_serie' => 'Série Alfa',
        'etapa_curso' => 2,
        'concluinte' => 1,
        'carga_horaria' => '800,00',
        'dias_letivos' => 200,
        'regras_avaliacao_id' => [$this->rule->getKey()],
        'regras_avaliacao_diferenciada_id' => [''],
        'anos_letivos' => [2026],
    ];

    $this->novo = fn (array $overrides = []) => LegacyFields::from(
        $this->get('/intranet/educar_serie_cad.php')->assertOk()->getContent(),
        $overrides + ($this->required)(),
    );

    $this->grade = LegacyGradeFactory::new()->create([
        'ref_cod_curso' => $this->course,
        'nm_serie' => 'Série Existente',
        'idade_inicial' => 6,
        'idade_final' => 7,
    ]);
    LegacyEvaluationRuleGradeYearFactory::new()->create([
        'serie_id' => $this->grade,
        'regra_avaliacao_id' => $this->rule,
        'regra_avaliacao_diferenciada_id' => null,
        'ano_letivo' => 2026,
    ]);

    $this->url = '/intranet/educar_serie_cad.php?cod_serie=' . $this->grade->getKey();
    $this->edicao = fn (array $overrides = []) => LegacyFields::from($this->get($this->url)->assertOk()->getContent(), $overrides);

    // Ano letivo => [regra, regra diferenciada] da série, como está no banco.
    $this->rules = fn (int $grade) => DB::table('modules.regra_avaliacao_serie_ano')
        ->where('serie_id', $grade)
        ->orderBy('ano_letivo')
        ->get()
        ->mapWithKeys(fn ($row) => [$row->ano_letivo => [$row->regra_avaliacao_id, $row->regra_avaliacao_diferenciada_id]])
        ->all();
});

test('abre a edição com os dados da série, as etapas do curso e as regras por ano', function () {
    $response = $this->get($this->url)->assertOk();
    $fields = LegacyFields::from($response->getContent());
    $form = LegacyForm::from($response->getContent());

    expect($fields['tipoacao'])->toBe('Editar')
        ->and($fields['nm_serie'])->toBe('Série Existente')
        ->and($fields['carga_horaria'])->toBe('800,00')
        ->and($fields['anos_letivos'])->toBe(['2026'])
        ->and($fields['regras_avaliacao_id'])->toBe([(string) $this->rule->getKey()])
        // Uma etapa para cada etapa do curso.
        ->and(array_values(array_filter(array_keys($form->options('etapa_curso')))))->toBe([1, 2, 3, 4])
        ->and($response->getContent())->not->toContain('excluirSerieComTurmas();');
});

test('avisa na edição quando a série tem turmas vinculadas', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create();

    $this->get('/intranet/educar_serie_cad.php?cod_serie=' . $schoolClass->ref_ref_cod_serie)
        ->assertOk()
        ->assertSee('excluirSerieComTurmas();', false);
});

test('cadastra uma série com todos os campos e as regras de dois anos', function () {
    $payload = ($this->novo)([
        'concluinte' => 2,
        'carga_horaria' => '1.000,50',
        'idade_inicial' => 6,
        'idade_final' => 7,
        'idade_ideal' => 6,
        'descricao' => 'anos iniciais',
        'observacao_historico' => 'Observação do histórico',
        'etapa_educacenso' => 14,
        'alerta_faixa_etaria' => 'on',
        'bloquear_matricula_faixa_etaria' => 'on',
        'exigir_inep' => 'on',
        'importar_serie_pre_matricula' => 'on',
        'regras_avaliacao_id' => [$this->rule->getKey(), $this->rule->getKey()],
        'regras_avaliacao_diferenciada_id' => [$this->otherRule->getKey(), ''],
        'anos_letivos' => [2025, 2026],
    ]);

    expect($payload['tipoacao'])->toBe('Novo');

    $this->post('/intranet/educar_serie_cad.php', $payload)->assertRedirect('educar_serie_lst.php');

    $grade = LegacyGrade::query()->where('nm_serie', 'Série Alfa')->sole();

    expect($grade->getAttributes())
        ->ref_cod_curso->toBe($this->course->getKey())
        ->etapa_curso->toBe(2)
        ->concluinte->toBe(2)
        ->carga_horaria->toEqual(1000.5)
        ->dias_letivos->toEqual(200)
        ->idade_inicial->toEqual(6)
        ->idade_final->toEqual(7)
        ->idade_ideal->toEqual(6)
        ->descricao->toBe('anos iniciais')
        ->observacao_historico->toBe('Observação do histórico')
        ->etapa_educacenso->toBe(14)
        ->alerta_faixa_etaria->toBeTrue()
        ->bloquear_matricula_faixa_etaria->toBeTrue()
        ->exigir_inep->toBeTrue()
        ->importar_serie_pre_matricula->toBeTrue()
        ->ativo->toBe(1)
        ->ref_usuario_cad->toBe($this->user->getKey());

    expect(($this->rules)($grade->getKey()))->toBe([
        2025 => [$this->rule->getKey(), $this->otherRule->getKey()],
        2026 => [$this->rule->getKey(), null],
    ]);
});

test('cadastra uma série só com o obrigatório, com as caixas desmarcadas', function () {
    $this->post('/intranet/educar_serie_cad.php', ($this->novo)())->assertRedirect('educar_serie_lst.php');

    $grade = LegacyGrade::query()->where('nm_serie', 'Série Alfa')->sole();

    expect($grade->getAttributes())
        ->concluinte->toBe(1)
        ->carga_horaria->toEqual(800)
        ->idade_ideal->toBeNull()
        ->etapa_educacenso->toBeNull()
        ->alerta_faixa_etaria->toBeFalse()
        ->bloquear_matricula_faixa_etaria->toBeFalse()
        ->exigir_inep->toBeFalse()
        ->importar_serie_pre_matricula->toBeFalse();
});

test('recusa o cadastro e devolve o formulário com o que foi digitado', function (array $overrides, array $without, string $message) {
    $payload = array_diff_key(($this->novo)($overrides), array_flip($without));

    $response = $this->post('/intranet/educar_serie_cad.php', $payload)
        ->assertOk()
        ->assertSee($message);

    expect(LegacyFields::from($response->getContent())['nm_serie'])->toBe('Série Alfa')
        ->and(LegacyGrade::query()->where('nm_serie', 'Série Alfa')->exists())->toBeFalse();
})->with([
    'carga horária inválida' => [['carga_horaria' => 'oitocentas'], [], 'Carga horária inválida.'],
    'sem a tabela de regras' => [[], ['regras_avaliacao_id', 'regras_avaliacao_diferenciada_id', 'anos_letivos'], 'Regra de avaliação não cadastrada'],
    'sem o curso' => [['ref_cod_curso' => ''], [], 'Cadastro não realizado.'],
]);

test('recusa a edição pelas mesmas regras', function (array $overrides, array $without, string $message) {
    $payload = array_diff_key(($this->edicao)($overrides + ['nm_serie' => 'Série Mudada']), array_flip($without));

    $this->post($this->url, $payload)->assertOk()->assertSee($message);

    expect($this->grade->refresh()->nm_serie)->toBe('Série Existente');
})->with([
    'carga horária inválida' => [['carga_horaria' => 'oitocentas'], [], 'Carga horária inválida.'],
    'sem a tabela de regras' => [[], ['regras_avaliacao_id', 'regras_avaliacao_diferenciada_id', 'anos_letivos'], 'Regra de avaliação não cadastrada'],
]);

test('edita a série e sincroniza as regras: troca a de um ano, acrescenta outro e remove o que saiu', function () {
    LegacyEvaluationRuleGradeYearFactory::new()->create([
        'serie_id' => $this->grade,
        'regra_avaliacao_id' => $this->rule,
        'regra_avaliacao_diferenciada_id' => null,
        'ano_letivo' => 2024,
    ]);

    $payload = ($this->edicao)([
        'nm_serie' => 'Série Renomeada',
        'idade_ideal' => 6,
        'observacao_historico' => 'Observação nova',
        'regras_avaliacao_id' => [$this->otherRule->getKey(), $this->rule->getKey()],
        'regras_avaliacao_diferenciada_id' => ['', $this->otherRule->getKey()],
        'anos_letivos' => [2026, 2027],
    ]);

    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($this->url, $payload)->assertRedirect('educar_serie_lst.php');

    expect($this->grade->refresh()->getAttributes())
        ->nm_serie->toBe('Série Renomeada')
        ->idade_ideal->toEqual(6)
        ->observacao_historico->toBe('Observação nova')
        ->ref_usuario_exc->toBe($this->user->getKey());

    expect(($this->rules)($this->grade->getKey()))->toBe([
        2026 => [$this->otherRule->getKey(), null],
        2027 => [$this->rule->getKey(), $this->otherRule->getKey()],
    ]);
});

// Defeito conhecido: a tela repassa à classe dois campos que o formulário não
// tem (a regra "da série", no singular), sempre vazios, e a edição grava NULL
// na regra diferenciada que a série tinha.
test('defeito: editar a série apaga a regra diferenciada gravada nela', function () {
    DB::table('pmieducar.serie')->where('cod_serie', $this->grade->getKey())->update([
        'regra_avaliacao_diferenciada_id' => $this->otherRule->getKey(),
    ]);

    $this->post($this->url, ($this->edicao)())->assertRedirect('educar_serie_lst.php');

    expect($this->grade->refresh()->getAttributes()['regra_avaliacao_diferenciada_id'])->toBeNull();
});

test('não exclui a série que tem turmas vinculadas', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create();
    $url = '/intranet/educar_serie_cad.php?cod_serie=' . $schoolClass->ref_ref_cod_serie;

    $this->post($url, ['tipoacao' => 'Excluir'] + LegacyFields::from($this->get($url)->assertOk()->getContent()))
        ->assertOk()
        ->assertSee('Não foi possível excluir a série, pois a mesma possui turmas vinculadas.');

    expect($schoolClass->grade->refresh()->ativo)->toBe(1);
});

test('excluir a série só desativa e mantém as idades dela', function () {
    $this->post($this->url, ['tipoacao' => 'Excluir'] + ($this->edicao)())->assertRedirect('educar_serie_lst.php');

    expect($this->grade->refresh()->getAttributes())
        ->ativo->toBe(0)
        ->ref_usuario_exc->toBe($this->user->getKey())
        ->idade_inicial->toEqual(6)
        ->idade_final->toEqual(7);
});

test('avisa quando a edição ou a exclusão não é gravada', function (string $action, string $message) {
    $payload = ['cod_serie' => 'abc', 'tipoacao' => $action] + ($this->edicao)();

    $this->post('/intranet/educar_serie_cad.php', $payload)->assertOk()->assertSee($message);

    expect($this->grade->refresh()->ativo)->toBe(1);
})->with([
    ['Editar', 'Edição não realizada.'],
    ['Excluir', 'Exclusão não realizada.'],
]);

test('volta para a listagem quando o usuário só pode visualizar série', function () {
    LegacyGeneralConfigurationFactory::new()->create($this->institution + ['active_on_ieducar' => true]);

    $this->actingAs(LegacyUserFactory::new()->institutional()->withAccess(583, modify: false, remove: false)->create());

    $this->get($this->url)->assertRedirect('educar_serie_lst.php');
});
