<?php

use App\Models\LegacyDisciplineDependence;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacyEvaluationRuleFactory;
use Database\Factories\LegacyEvaluationRuleGradeYearFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacySchoolGradeDisciplineFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\EnrollmentScenario;
use Tests\Request\Support\LegacyFields;
use Tests\Request\Support\LegacyForm;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    $this->registration = $this->scenario->registration(['ref_cod_aluno' => $this->scenario->student('Ana Alves'), 'dependencia' => true]);
    $this->scenario->enroll($this->registration, $this->scenario->schoolClass(['nm_turma' => 'Turma Alfa']));

    $this->arts = $this->scenario->discipline('Artes Visuais');
    $this->botany = $this->scenario->discipline('Botânica');

    // A regra de avaliação da série limita as disciplinas de dependência.
    $this->rule = fn (int $limit) => $this->scenario->evaluationRule(['qtd_disciplinas_dependencia' => $limit]);

    $this->new = '/intranet/educar_disciplina_dependencia_cad.php?ref_cod_matricula=' . $this->registration->getKey();
    $this->list = 'educar_disciplina_dependencia_lst.php?ref_cod_matricula=' . $this->registration->getKey();
    $this->edit = fn ($discipline) => $this->new . '&ref_cod_disciplina=' . $discipline->getKey();

    $this->dependence = fn ($discipline, string $note = 'Observação original') => LegacyDisciplineDependence::query()->create([
        'cod_disciplina_dependencia' => (LegacyDisciplineDependence::query()->max('cod_disciplina_dependencia') ?? 0) + 1,
        'ref_cod_matricula' => $this->registration->getKey(),
        'ref_cod_serie' => $this->scenario->grade->getKey(),
        'ref_cod_escola' => $this->scenario->school->getKey(),
        'ref_cod_disciplina' => $discipline->getKey(),
        'observacao' => $note,
    ]);

    $this->saved = fn () => LegacyDisciplineDependence::query()
        ->where('ref_cod_matricula', $this->registration->getKey())
        ->pluck('observacao', 'ref_cod_disciplina')
        ->all();

    $this->payload = fn (array $overrides = []) => LegacyFields::from($this->get($this->new)->assertOk()->getContent(), $overrides + [
        'ref_cod_disciplina' => $this->arts->getKey(),
        'observacao' => 'Reprovou por nota',
    ]);
});

test('abre o cadastro com o aluno e os componentes da turma', function () {
    $html = $this->get($this->new)->assertOk()->assertSee('Ana Alves')->getContent();
    $fields = LegacyFields::from($html);

    expect($fields['tipoacao'])->toBe('Novo')
        ->and($fields['ref_cod_matricula'])->toBe((string) $this->registration->getKey())
        ->and($fields['ref_cod_serie'])->toBe((string) $this->scenario->grade->getKey())
        ->and($fields['ref_cod_escola'])->toBe((string) $this->scenario->school->getKey())
        ->and(array_values(LegacyForm::from($html)->options('ref_cod_disciplina')))->toBe(['Selecione', 'Artes Visuais', 'Botânica']);
});

test('avisa que a matrícula precisa estar enturmada', function () {
    $registration = $this->scenario->registration();

    $this->get('/intranet/educar_disciplina_dependencia_cad.php?ref_cod_matricula=' . $registration->getKey())
        ->assertOk()
        ->assertSee('Para cadastrar uma disciplina de dependência de um aluno, é necessário que este esteja enturmado.');
});

test('cadastra a disciplina de dependência', function () {
    ($this->rule)(2);

    $this->post($this->new, ($this->payload)())->assertRedirect($this->list);

    expect(($this->saved)())->toBe([$this->arts->getKey() => 'Reprovou por nota']);
});

test('cadastrar de novo a mesma disciplina só atualiza a observação', function () {
    ($this->rule)(2);
    ($this->dependence)($this->arts);

    $this->post($this->new, ($this->payload)(['observacao' => 'Observação nova']))->assertRedirect($this->list);

    expect(($this->saved)())->toBe([$this->arts->getKey() => 'Observação nova']);
});

// Defeito conhecido: a tela monta o aviso do limite com `$this->mensagem .=`.
// A classe base só intercepta a atribuição simples; o `.=` cria uma
// propriedade solta e o aviso se perde. O usuário vê a mensagem genérica.
test('defeito: recusa além do limite da regra de avaliação, mas com a mensagem genérica', function () {
    ($this->rule)(1);
    ($this->dependence)($this->botany);

    $this->post($this->new, ($this->payload)())
        ->assertOk()
        ->assertSee('Não foi possível inserir a informação. [CAD01]')
        ->assertDontSee('A regra desta série limita a quantidade de disciplinas de dependência');

    expect(($this->saved)())->toHaveCount(1);
});

test('usa o limite da regra diferenciada quando a escola a utiliza', function () {
    LegacyEvaluationRuleGradeYearFactory::new()->create([
        'regra_avaliacao_id' => LegacyEvaluationRuleFactory::new()->create(['qtd_disciplinas_dependencia' => 0]),
        'regra_avaliacao_diferenciada_id' => LegacyEvaluationRuleFactory::new()->create(['qtd_disciplinas_dependencia' => 3]),
        'serie_id' => $this->scenario->grade,
        'ano_letivo' => 2030,
    ]);

    $this->post($this->new, ($this->payload)())->assertOk()->assertSee('Não foi possível inserir a informação. [CAD01]');

    expect(($this->saved)())->toBe([]);

    $this->scenario->school->update(['utiliza_regra_diferenciada' => true]);

    $this->post($this->new, ($this->payload)())->assertRedirect($this->list);

    expect(($this->saved)())->toHaveKey($this->arts->getKey());
});

// Defeito conhecido: sem regra de avaliação da série no ano, o limite é nulo e
// a comparação recusa qualquer disciplina.
test('defeito: sem regra de avaliação no ano recusa qualquer disciplina', function () {
    $this->post($this->new, ($this->payload)())->assertOk()->assertSee('Não foi possível inserir a informação. [CAD01]');

    expect(($this->saved)())->toBe([]);
});

test('recusa o componente que não está habilitado na série da escola', function () {
    ($this->rule)(2);

    $loose = LegacyDisciplineFactory::new()->create(['nome' => 'Componente Avulso']);

    $this->post($this->new, ($this->payload)(['ref_cod_disciplina' => $loose->getKey()]))
        ->assertOk()
        ->assertSee('O componente não está habilitado na série da escola.');

    expect(($this->saved)())->toBe([]);
});

test('recusa o componente habilitado na escola mas sem vínculo com a série', function () {
    ($this->rule)(2);

    $loose = LegacyDisciplineFactory::new()->create(['nome' => 'Componente Avulso']);

    LegacySchoolGradeDisciplineFactory::new()->create([
        'ref_ref_cod_escola' => $this->scenario->school,
        'ref_ref_cod_serie' => $this->scenario->grade,
        'ref_cod_disciplina' => $loose,
        'anos_letivos' => '{2030}',
    ]);

    $this->post($this->new, ($this->payload)(['ref_cod_disciplina' => $loose->getKey()]))->assertOk()->assertSee('Cadastro não realizado.');

    expect(($this->saved)())->toBe([]);
});

test('abre a edição com o componente fixo e a observação gravada', function () {
    ($this->dependence)($this->arts);

    $fields = LegacyFields::from($this->get(($this->edit)($this->arts))->assertOk()->assertSee('Artes Visuais')->getContent());

    expect($fields['tipoacao'])->toBe('Editar')
        ->and($fields['ref_cod_disciplina'])->toBe((string) $this->arts->getKey())
        ->and($fields['observacao'])->toBe('Observação original');
});

test('edita a observação da disciplina de dependência', function () {
    ($this->dependence)($this->arts);

    $payload = LegacyFields::from($this->get(($this->edit)($this->arts))->assertOk()->getContent(), ['observacao' => 'Observação alterada']);

    $this->post(($this->edit)($this->arts), $payload)->assertRedirect($this->list);

    expect(($this->saved)())->toBe([$this->arts->getKey() => 'Observação alterada']);
});

test('exclui a disciplina de dependência', function () {
    ($this->dependence)($this->arts);
    ($this->dependence)($this->botany);

    $payload = LegacyFields::from($this->get(($this->edit)($this->arts))->assertOk()->getContent(), ['tipoacao' => 'Excluir']);

    $this->post(($this->edit)($this->arts), $payload)->assertRedirect($this->list);

    expect(array_keys(($this->saved)()))->toBe([$this->botany->getKey()]);
});

test('avisa quando a disciplina a editar ou excluir não é dependência da matrícula', function (string $action, string $message) {
    ($this->dependence)($this->arts);

    $payload = LegacyFields::from($this->get(($this->edit)($this->arts))->assertOk()->getContent(), [
        'tipoacao' => $action,
        'ref_cod_disciplina' => $this->botany->getKey(),
    ]);

    $this->post(($this->edit)($this->arts), $payload)->assertOk()->assertSee($message);

    expect(($this->saved)())->toBe([$this->arts->getKey() => 'Observação original']);
})->with([
    ['Editar', 'Edição não realizada.'],
    ['Excluir', 'Exclusão não realizada.'],
]);

test('com um componente que não é dependência da matrícula abre como cadastro', function () {
    $fields = LegacyFields::from($this->get(($this->edit)($this->arts))->assertOk()->getContent());

    expect($fields['tipoacao'])->toBe('Novo');
});

test('volta para a listagem de matrículas sem matrícula ou com matrícula inexistente', function () {
    $this->get('/intranet/educar_disciplina_dependencia_cad.php')->assertRedirect('educar_matricula_lst.php');
    $this->get('/intranet/educar_disciplina_dependencia_cad.php?ref_cod_matricula=999999999')->assertRedirect('educar_matricula_lst.php');
});

// Defeito conhecido: o redirecionamento de quem não pode cadastrar leva o
// parâmetro com o nome errado (`ref_ref_cod_matricula`), e a listagem não
// reconhece a matrícula.
test('defeito: quem só pode visualizar volta para a listagem de dependências sem a matrícula', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(578, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->scenario->institution])
    );

    $this->get($this->new)->assertRedirect('educar_disciplina_dependencia_lst.php?ref_ref_cod_matricula=' . $this->registration->getKey());
});
