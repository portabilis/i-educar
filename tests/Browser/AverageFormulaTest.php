<?php

use App\Models\LegacyAverageFormula;
use Tests\Browser\Support\Form;
use Tests\Browser\Support\Scenario;

/**
 * Escola > Cadastros > Tipos > Regras de avaliação > Fórmulas de cálculo de média.
 *
 * Mesmo feitio do /module/AreaConhecimento (ver KnowledgeAreaTest), com dois
 * campos a mais: a fórmula, que passa por um validador próprio, e o tipo, que é
 * um `campoRadio` — o único destas telas de tipos que não é `<select>`.
 *
 * A fórmula usada é a mesma da "Média aritmética" que a instalação traz em
 * database/sqls/inserts/modules.formula_media.sql, para o teste não depender de
 * adivinhar quais variáveis o FormulaMedia_Validate_Formula aceita.
 */
beforeEach(function () {
    $this->scenario = Scenario::install(adminLogin: 'adm-formula');
});

/**
 * Cria uma fórmula por baixo, para os testes que exercitam a edição e a
 * exclusão. A do cenário não serve: ela é a da regra de avaliação padrão, e a
 * tela recusa excluir fórmula vinculada a regra.
 */
function averageFormula(int $institution, string $name = 'Média aritmética'): LegacyAverageFormula
{
    return LegacyAverageFormula::query()->create([
        'instituicao_id' => $institution,
        'nome' => $name,
        'formula_media' => 'Se / Et',
        'tipo_formula' => 1,
    ]);
}

it('lista as fórmulas de cálculo de média', function () {
    averageFormula($this->scenario->institution->getKey());

    $this->scenario->loginAsAdmin()
        ->navigate('/module/FormulaMedia/index')
        ->assertSee('Listagem de fórmulas de cálculo de média')
        ->assertSee('Média aritmética');
});

it('cadastra uma fórmula de cálculo de média', function () {
    $page = $this->scenario->loginAsAdmin()
        ->navigate('/module/FormulaMedia/edit');

    $page = Form::cascade($page, ['instituicao' => $this->scenario->institution->getKey()]);

    $page = Form::fill($page, [
        'nome' => 'Média aritmética',
        'formulaMedia' => 'Se / Et',
    ])->radio('tipoFormula', '1');

    Form::save($page)
        ->assertPathIs('/module/FormulaMedia/index')
        ->assertSee('Média aritmética');

    $formula = LegacyAverageFormula::query()->where('nome', 'Média aritmética')->sole();

    expect($formula->formula_media)->toBe('Se / Et')
        ->and((int) $formula->tipo_formula)->toBe(1);
});

it('edita o nome da fórmula de cálculo de média', function () {
    $formula = averageFormula($this->scenario->institution->getKey());

    $page = $this->scenario->loginAsAdmin()
        ->navigate('/module/FormulaMedia/edit?id=' . $formula->getKey())
        ->fill('nome', 'Média aritmética simples');

    // Editar volta para o detalhe, e não para a listagem.
    Form::save($page)->assertPathIs('/module/FormulaMedia/view');

    expect($formula->fresh()->nome)->toBe('Média aritmética simples');
});

it('exclui uma fórmula de cálculo de média', function () {
    $formula = averageFormula($this->scenario->institution->getKey());

    $page = $this->scenario->loginAsAdmin()
        ->navigate('/module/FormulaMedia/edit?id=' . $formula->getKey());

    Form::remove($page)
        ->assertPathIs('/module/FormulaMedia/index')
        ->assertDontSee('Média aritmética');

    expect(LegacyAverageFormula::query()->find($formula->getKey()))->toBeNull();
});

it('recusa gravar sem o nome da fórmula', function () {
    $formulas = LegacyAverageFormula::query()->count();

    $page = $this->scenario->loginAsAdmin()
        ->navigate('/module/FormulaMedia/edit');

    $page = Form::fill($page, ['formulaMedia' => 'Se / Et'])->radio('tipoFormula', '1');

    Form::reject($page)->assertPathIs('/module/FormulaMedia/edit');

    // O cenário já traz a fórmula da regra de avaliação padrão, então o que se
    // afirma é que nada foi acrescentado.
    expect(LegacyAverageFormula::query()->count())->toBe($formulas);
});
