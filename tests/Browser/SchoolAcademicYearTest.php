<?php

use App\Models\LegacySchoolAcademicYear;
use Tests\Browser\Support\Form;
use Tests\Browser\Support\Scenario;

/**
 * Etapa 7 do roteiro: abrir o ano letivo da escola e definir suas etapas.
 *
 * São duas telas encadeadas. A primeira só escolhe o ano e o botão se chama
 * "Continuar", não "Salvar"; ela redireciona para a segunda, que escolhe o tipo
 * de etapa — o catálogo do cenário traz "Bimestre - 4 etapa(s)" — e recebe as
 * datas de cada etapa numa tabela dinâmica.
 *
 * A escola vem por factory: o cadastro dela pela interface ainda não fecha, e
 * está registrado em SchoolTest.
 */
beforeEach(function () {
    $this->scenario = Scenario::install(adminLogin: 'adm-ano');
    $this->school = Scenario::school();
    $this->year = now()->year;
});

it('abre a definição do ano letivo com os anos disponíveis', function () {
    $this->scenario->loginAsAdmin()
        ->navigate('/intranet/educar_escola_ano_letivo_cad.php?cod_escola=' . $this->school->getKey())
        ->assertSee('Ano')
        ->assertValue('#btn_enviar', 'Continuar');
});

it('cadastra o ano letivo com as etapas', function () {
    $page = $this->scenario->loginAsAdmin()
        ->navigate('/intranet/educar_escola_ano_letivo_cad.php?cod_escola=' . $this->school->getKey());

    $page = Form::cascade($page, ['ano' => (string) $this->year]);

    // A primeira tela apenas escolhe o ano e leva para a das etapas.
    $page = Form::save($page, 'Continuar')
        ->assertPathIs('/intranet/educar_ano_letivo_modulo_cad.php');

    // O tipo de etapa do catálogo já vem selecionado por ser o único; a asserção
    // garante que ele chegou à tela, sem reselecionar — reselecionar remonta a
    // tabela de etapas e perde o que foi digitado.
    // assertSourceHas, e não assertSee: o texto está numa <option> de select
    // fechado, que não conta como visível, e as asserções de texto ficam
    // repetindo até o teste travar em vez de falhar.
    $page = $page->assertSourceHas(Scenario::STAGE_TYPE . ' - 4 etapa(s)');

    $page = Form::fill($page, [
        '[name="data_inicio[0]"]' => '01/02/' . $this->year,
        '[name="data_fim[0]"]' => '15/12/' . $this->year,
        '[name="dias_letivos[0]"]' => '200',
    ]);

    Form::save($page)->assertPathIs('/intranet/educar_escola_det.php');

    $academicYear = LegacySchoolAcademicYear::query()
        ->where('ref_cod_escola', $this->school->getKey())
        ->where('ano', $this->year)
        ->sole();

    expect($academicYear->academicYearStages()->count())->toBeGreaterThan(0);
});

it('recusa etapa sem as datas', function () {
    $page = $this->scenario->loginAsAdmin()
        ->navigate('/intranet/educar_escola_ano_letivo_cad.php?cod_escola=' . $this->school->getKey());

    $page = Form::cascade($page, ['ano' => (string) $this->year]);
    $page = Form::save($page, 'Continuar');

    // Data inicial e final são obrigatórias, e a validação é do clsCadastro, em
    // JS: a tela não avança. Ordem entre elas, porém, não é conferida em lugar
    // nenhum — o AcademicYearService só checa conflito com outros anos letivos.
    Form::save($page)->assertPathIs('/intranet/educar_ano_letivo_modulo_cad.php');

    expect(LegacySchoolAcademicYear::query()->where('ref_cod_escola', $this->school->getKey())->count())->toBe(0);
});
