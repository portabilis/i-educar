<?php

use App\Models\LegacyEvaluationRule;
use Tests\Browser\Support\Form;
use Tests\Browser\Support\Scenario;

/**
 * Escola > Cadastros > Regras de avaliação.
 *
 * É a quinta e última tela da família `/module/…` — a mesma do KnowledgeAreaTest
 * por baixo, um Core_Controller_Page_EditController sobre clsCadastro —, e a
 * maior delas: o Gerar() tem mais de 600 linhas e 36 campos. Também é a que
 * fecha o encadeamento das duas anteriores: a regra escolhe uma fórmula de
 * cálculo de média e uma tabela de arredondamento, que são exatamente as telas
 * cobertas por AverageFormulaTest e RoundingTableTest.
 *
 * O cenário já instala uma regra padrão, usada pela série; por isso os testes
 * daqui trabalham com uma segunda, própria, e não com aquela.
 */
beforeEach(function () {
    $this->scenario = Scenario::install(adminLogin: 'adm-regra');
});

it('lista as regras de avaliação', function () {
    $this->scenario->loginAsAdmin()
        ->navigate('/module/RegraAvaliacao/index')
        ->assertSee('Listagem de regras de avaliação')
        ->assertSee(Scenario::EVALUATION_RULE);
});

it('cadastra uma regra de avaliação', function () {
    $padrao = $this->scenario->evaluationRule;

    $page = $this->scenario->loginAsAdmin()
        ->navigate('/module/RegraAvaliacao/edit');

    $page = Form::cascade($page, ['instituicao' => $this->scenario->institution->getKey()]);

    $page = Form::fill($page, ['nome' => 'Regra de Avaliação Numérica'])
        ->radio('tipoNota', '1');

    // O select visível é `tabelaArredondamento`; os irmãos
    // `tabelaArredondamentoNumero` e `tabelaArredondamentoConceitual` ficam
    // ocultos, e escolher num campo oculto faz o Playwright esperar para sempre.
    // A escolha é pelo id porque os nomes vêm do factory do cenário.
    $page = Form::cascade($page, [
        'tabelaArredondamento' => $padrao->tabela_arredondamento_id,
        'formulaMedia' => $padrao->formula_media_id,
    ]);

    // Os numéricos são todos obrigatórios e chegam vazios: sem eles o valida()
    // barra o envio.
    $page = Form::fill($page, [
        'media' => '7',
        'porcentagemPresenca' => '75',
        'notaMaximaGeral' => '10',
        'notaMinimaGeral' => '0',
        'faltaMaximaGeral' => '999',
        'faltaMinimaGeral' => '0',
        'notaMaximaExameFinal' => '10',
        'qtdCasasDecimais' => '1',
        'qtdDisciplinasDependencia' => '0',
        'qtdMatriculasDependencia' => '0',
        'reprovarAutomaticamenteAposDependencias' => '0',
        // "Pontos" é opcional no formulário, mas deixá-lo vazio derruba a tela:
        // o isPontosValid() é declarado `string` e o ConvertEmptyStringsToNull
        // manda null. A própria regex do método aceita vazio, então é defeito de
        // hoje, não regra de negócio.
        'pontos' => '10',
    ]);

    Form::save($page)
        ->assertPathIs('/module/RegraAvaliacao/index')
        ->assertSee('Regra de Avaliação Numérica');

    $regra = LegacyEvaluationRule::query()->where('nome', 'Regra de Avaliação Numérica')->sole();

    expect($regra->instituicao_id)->toBe($this->scenario->institution->getKey())
        ->and((int) $regra->tipo_nota)->toBe(1);
});

it('edita o nome da regra de avaliação', function () {
    $regra = $this->scenario->evaluationRule;

    $page = $this->scenario->loginAsAdmin()
        ->navigate('/module/RegraAvaliacao/edit?id=' . $regra->getKey())
        ->fill('nome', 'Regra de Avaliação Revisada')
        // Pelo mesmo defeito do isPontosValid(), gravar com "Pontos" vazio
        // derruba a tela — inclusive numa edição que só mexe no nome.
        ->fill('pontos', '10');

    // Editar volta para o detalhe, e não para a listagem.
    Form::save($page)->assertPathIs('/module/RegraAvaliacao/view');

    expect($regra->fresh()->nome)->toBe('Regra de Avaliação Revisada');
});

it('recusa gravar a regra sem o nome', function () {
    $regras = LegacyEvaluationRule::query()->count();

    $page = $this->scenario->loginAsAdmin()
        ->navigate('/module/RegraAvaliacao/edit');

    $page = Form::cascade($page, ['instituicao' => $this->scenario->institution->getKey()]);

    Form::reject($page)->assertPathIs('/module/RegraAvaliacao/edit');

    // O cenário já traz a regra padrão, então o que se afirma é que nada foi
    // acrescentado.
    expect(LegacyEvaluationRule::query()->count())->toBe($regras);
});
