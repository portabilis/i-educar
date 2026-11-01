<?php

use App\Models\City;
use App\Models\DocumentAbsenceReason;
use Tests\Browser\Support\Category;
use Tests\Browser\Support\Form;
use Tests\Browser\Support\Scenario;

/**
 * As telas de catálogo: os três menus "Tipos" — Pessoas, Escola e Servidores —
 * e os quatro cadastros de Endereçamento.
 *
 * São as telas mais simples do sistema, e todas do mesmo feitio: um clsListagem
 * com um campo de busca e um clsCadastro com um campo de texto obrigatório, mais
 * um ou outro campo acessório. É por serem iguais que aqui elas são dados, e não
 * vinte e três arquivos de teste — o que muda de uma para a outra está declarado
 * em Category, e o roteiro é escrito uma vez só.
 *
 * O que estas telas guardam contra o rebranding é o título que a listagem
 * escreve no topo, o rótulo dos botões (conferido pelo Form) e o caminho de ida
 * e volta entre listagem e formulário.
 *
 * O login do administrador é próprio deste arquivo porque
 * `test:browser:parallel` roda os processos no mesmo banco `testing`; ele é
 * curto porque portal.funcionario.matricula é `character varying(12)`.
 */
beforeEach(function () {
    $this->scenario = Scenario::install(adminLogin: 'adm-tipos');

    // O que o cenário montou e só se sabe em tempo de execução. Cada descritor
    // escolhe daqui os campos que o formulário dele tem; quem não tem nenhum
    // ignora o mapa inteiro.
    $this->context = [
        'ref_cod_instituicao' => $this->scenario->institution->getKey(),
        'idpais' => $this->scenario->state->country_id,
        'iduf' => $this->scenario->state->getKey(),
        'idmun' => City::query()->where('state_id', $this->scenario->state->getKey())->sole()->getKey(),
    ];
});

it('lista', function (Category $category) {
    $this->scenario->loginAsAdmin()
        ->navigate($category->list())
        ->assertSee($category->heading);
})->with(Category::all());

it('cadastra, edita e exclui', function (Category $category) {
    $page = $this->scenario->loginAsAdmin()->navigate($category->form());

    $page = $category->fill($page, $category->name, $this->context);

    $page = Form::save($page)
        ->assertPathIs($category->list())
        ->assertSee($category->name);

    $record = $category->record($category->name);

    expect($record)->not->toBeNull();

    $page = $page->navigate($category->form($record->getKey()))
        ->fill($category->field, $category->renamed);

    $page = Form::save($page)
        ->assertPathIs($category->list())
        ->assertSee($category->renamed);

    // Relê pelo nome novo, em vez de conferir o atributo do modelo: os modelos
    // Legacy* traduzem nomes de coluna, e nem todas estas telas usam o mesmo
    // nome no formulário e no banco (nm_religiao é gravado em name).
    expect($category->record($category->renamed)?->getKey())->toBe($record->getKey());

    if (!$category->removable) {
        return;
    }

    $page = $page->navigate($category->form($record->getKey()));

    Form::remove($page)
        ->assertPathIs($category->list())
        ->assertDontSee($category->renamed);
})->with(Category::all());

it('recusa gravar sem o campo obrigatório', function (Category $category) {
    $records = $category->count();

    $page = $this->scenario->loginAsAdmin()->navigate($category->form());

    $page = $category->fill($page, '', $this->context);

    // O valida() do clsCadastro barra o submit: a tela continua sendo a do
    // formulário e a tabela continua do mesmo tamanho.
    Form::reject($page)->assertPathIs($category->form());

    expect($category->count())->toBe($records);
})->with(Category::all());

/**
 * Duas telas ficam só com a listagem no conjunto acima, e têm o resto aqui.
 *
 * "Motivos de ausência de documentação" não tem Excluir(): o registro sai de
 * circulação pelo campo "Situação", que grava deleted_at.
 *
 * "Tipos de evento do calendário" é a única destas telas que não pertence à
 * instituição, e sim a uma escola, e o cadastro dela fica esperando a escola
 * existir. Só a listagem é coberta aqui; o cadastro entra junto com o cenário de
 * escola, que está sendo montado à parte.
 */
it('inativa um motivo de ausência de documentação', function () {
    $reason = DocumentAbsenceReason::query()->create(['name' => 'Documento retido em cartório']);

    $page = $this->scenario->loginAsAdmin()
        ->navigate('/intranet/educar_motivo_ausencia_documentacao_cad.php?id=' . $reason->getKey())
        ->select('ativo', 'Inativo');

    Form::save($page)
        ->assertPathIs('/intranet/educar_motivo_ausencia_documentacao_lst.php')
        ->assertSee('Inativo');

    // fresh() não serve aqui: ele consulta sem os escopos globais e devolve o
    // registro mesmo depois do soft delete.
    expect(DocumentAbsenceReason::query()->find($reason->getKey()))->toBeNull()
        ->and(DocumentAbsenceReason::withTrashed()->find($reason->getKey())->trashed())->toBeTrue();
});

it('lista os tipos de evento do calendário', function () {
    $this->scenario->loginAsAdmin()
        ->navigate('/intranet/educar_calendario_dia_motivo_lst.php')
        ->assertSee('Calendário Dia Motivo - Listagem');
});
