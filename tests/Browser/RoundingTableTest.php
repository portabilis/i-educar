<?php

use App\Models\LegacyRoundingTable;
use Tests\Browser\Support\Form;
use Tests\Browser\Support\Scenario;

/**
 * Escola > Cadastros > Tipos > Regras de avaliação > Tabelas de arredondamento.
 *
 * Mesmo feitio do /module/AreaConhecimento (ver KnowledgeAreaTest), com duas
 * diferenças que o teste precisa respeitar.
 *
 * A primeira é que o formulário muda depois de gravar: no cadastro o sistema de
 * nota é um `campoRadio`; na edição ele vira um campo de texto desabilitado e
 * aparece a tabela das dez faixas de arredondamento, que só existem depois de a
 * tabela ter sido criada.
 *
 * A segunda é um defeito de hoje: o `_save()` da tela, no caminho da edição,
 * grava só `arredondarNota`, `normalizarMedia` e as faixas — **o nome digitado
 * é descartado**. Por isso aqui não há teste de renomear: o que a edição
 * realmente muda é o arredondamento, e é isso que está coberto. Quando o nome
 * passar a ser gravado, entra o teste que falta.
 *
 * Também não há teste de exclusão: `$_deleteOption` é `false` nesta tela, então
 * ela não oferece o botão "Excluir".
 */
beforeEach(function () {
    $this->scenario = Scenario::install(adminLogin: 'adm-tabela');
});

/**
 * Cria uma tabela por baixo, para os testes que exercitam a edição e a
 * exclusão. A do cenário não serve: ela é a da regra de avaliação padrão.
 */
function roundingTable(int $institution, string $name = 'Arredondamento padrão'): LegacyRoundingTable
{
    return LegacyRoundingTable::query()->create([
        'instituicao_id' => $institution,
        'nome' => $name,
        'tipo_nota' => 1,
    ]);
}

it('lista as tabelas de arredondamento', function () {
    roundingTable($this->scenario->institution->getKey());

    $this->scenario->loginAsAdmin()
        ->navigate('/module/TabelaArredondamento/index')
        ->assertSee('Listagem de tabelas de arredondamento de nota')
        ->assertSee('Arredondamento padrão');
});

it('cadastra uma tabela de arredondamento', function () {
    $page = $this->scenario->loginAsAdmin()
        ->navigate('/module/TabelaArredondamento/edit');

    $page = Form::cascade($page, ['instituicao' => $this->scenario->institution->getKey()]);

    $page = Form::fill($page, ['nome' => 'Arredondamento padrão'])
        ->radio('tipoNota', '1')
        ->select('arredondarNota', 'Sim');

    Form::save($page)
        ->assertPathIs('/module/TabelaArredondamento/index')
        ->assertSee('Arredondamento padrão');

    $table = LegacyRoundingTable::query()->where('nome', 'Arredondamento padrão')->sole();

    expect((int) $table->tipo_nota)->toBe(1)
        ->and((int) $table->arredondar_nota)->toBe(1);
});

it('liga o arredondamento da nota da etapa', function () {
    $table = roundingTable($this->scenario->institution->getKey());

    expect((int) $table->arredondar_nota)->toBe(0);

    $page = $this->scenario->loginAsAdmin()
        ->navigate('/module/TabelaArredondamento/edit?id=' . $table->getKey())
        ->select('arredondarNota', 'Sim');

    // Editar volta para o detalhe, e não para a listagem.
    Form::save($page)->assertPathIs('/module/TabelaArredondamento/view');

    expect((int) $table->fresh()->arredondar_nota)->toBe(1);
});

it('recusa gravar sem o nome da tabela de arredondamento', function () {
    $tables = LegacyRoundingTable::query()->count();

    $page = $this->scenario->loginAsAdmin()
        ->navigate('/module/TabelaArredondamento/edit');

    $page = $page->radio('tipoNota', '1');

    Form::reject($page)->assertPathIs('/module/TabelaArredondamento/edit');

    // O cenário já traz a tabela da regra de avaliação padrão, então o que se
    // afirma é que nada foi acrescentado.
    expect(LegacyRoundingTable::query()->count())->toBe($tables);
});
