<?php

use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyEvaluationRuleFactory;
use Database\Factories\LegacyEvaluationRuleGradeYearFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->institution = ['ref_cod_instituicao' => $this->user->ref_cod_instituicao];
    $this->course = LegacyCourseFactory::new()->create($this->institution + ['nm_curso' => 'Curso Alfa']);

    $this->grade = LegacyGradeFactory::new()->create([
        'ref_cod_curso' => $this->course,
        'nm_serie' => 'Série Alfa',
        'etapa_curso' => 2,
        'concluinte' => 2,
        'carga_horaria' => 800,
        'dias_letivos' => 200,
        'idade_ideal' => 6,
        'observacao_historico' => 'Observação do histórico',
    ]);

    $this->url = '/intranet/educar_serie_det.php?cod_serie=' . $this->grade->getKey();
});

test('mostra todos os dados da série ao administrador', function () {
    $this->get($this->url)
        ->assertOk()
        ->assertSeeInOrder(['Instituição', $this->course->institution->nm_instituicao], false)
        ->assertSeeInOrder(['Curso', 'Curso Alfa'])
        ->assertSeeInOrder(['Série', 'Série Alfa'])
        ->assertSeeInOrder(['Etapa Curso', '2'])
        ->assertSeeInOrder(['Concluinte', 'sim'])
        ->assertSeeInOrder(['Carga Horária', '800'])
        ->assertSeeInOrder(['Dias letivos', '200'])
        ->assertSeeInOrder(['Idade padrão', '6'])
        ->assertSeeInOrder(['Observação histórico', 'Observação do histórico'])
        ->assertDontSee('Regras de avaliação')
        ->assertSee('educar_serie_cad.php?cod_serie=' . $this->grade->getKey(), false);
});

test('mostra a série não concluinte', function () {
    $this->grade->update(['concluinte' => 1]);

    $this->get($this->url)->assertOk()->assertSeeInOrder(['Concluinte', 'não']);
});

test('lista as regras de avaliação da série, cada uma com os anos em que vale', function () {
    $rule = LegacyEvaluationRuleFactory::new()->create(['nome' => 'Regra Alfa']);
    $other = LegacyEvaluationRuleFactory::new()->create(['nome' => 'Regra Beta']);

    foreach ([2025 => $rule, 2026 => $rule, 2027 => $other] as $year => $yearRule) {
        LegacyEvaluationRuleGradeYearFactory::new()->create([
            'serie_id' => $this->grade,
            'regra_avaliacao_id' => $yearRule,
            'regra_avaliacao_diferenciada_id' => null,
            'ano_letivo' => $year,
        ]);
    }

    $this->get($this->url)
        ->assertOk()
        ->assertSee('Regras de avaliação')
        ->assertSee('/module/RegraAvaliacao/view?id=' . $rule->getKey(), false)
        ->assertSee('Regra Alfa (2025, 2026)')
        ->assertSee('Regra Beta (2027)');
});

test('fora do nível de administrador não mostra a instituição nem os botões de quem só visualiza', function () {
    LegacyGeneralConfigurationFactory::new()->create($this->institution + ['active_on_ieducar' => true]);

    $this->actingAs(LegacyUserFactory::new()->institutional()->withAccess(583, modify: false, remove: false)->create());

    $this->get($this->url)
        ->assertOk()
        ->assertSee('Série Alfa')
        ->assertDontSee($this->course->institution->nm_instituicao)
        ->assertDontSee('educar_serie_cad.php?cod_serie=' . $this->grade->getKey(), false);
});

test('volta para a listagem quando a série não existe', function () {
    $this->get('/intranet/educar_serie_det.php?cod_serie=0')->assertRedirect('educar_serie_lst.php');
});
