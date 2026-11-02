<?php

use App\Models\LegacyKnowledgeArea;
use Tests\Browser\Support\Form;
use Tests\Browser\Support\Scenario;

/**
 * Escola > Cadastros > Tipos > Componentes curriculares > Áreas de conhecimento.
 *
 * Esta é uma das três telas do menu "Tipos" que não são um par
 * educar_*_lst/educar_*_cad: a listagem é um Core_Controller_Page_ListController
 * e o cadastro um Core_Controller_Page_EditController, servidos em
 * /module/AreaConhecimento/…. Por baixo os dois continuam sendo clsListagem e
 * clsCadastro — os campos e o botão "Salvar" são os mesmos, e por isso o Form
 * serve igual —, o que muda é o endereço e para onde cada ação volta: cadastrar
 * volta para a listagem, **editar volta para o detalhe** e excluir volta para a
 * listagem. Isso está em Core_Controller_Page_Abstract::$_options.
 */
beforeEach(function () {
    $this->scenario = Scenario::install(adminLogin: 'adm-area');
});

/**
 * Cria uma área de conhecimento por baixo, para os testes que exercitam a
 * edição e a exclusão. O cadastro pela interface é coberto por um teste próprio.
 */
function knowledgeArea(int $institution, string $name = 'Ciências da natureza'): LegacyKnowledgeArea
{
    return LegacyKnowledgeArea::query()->create([
        'instituicao_id' => $institution,
        'nome' => $name,
    ]);
}

it('lista as áreas de conhecimento', function () {
    knowledgeArea($this->scenario->institution->getKey());

    $this->scenario->loginAsAdmin()
        ->navigate('/module/AreaConhecimento/index')
        ->assertSee('Listagem de áreas de conhecimento')
        ->assertSee('Ciências da natureza');
});

it('cadastra uma área de conhecimento', function () {
    $page = $this->scenario->loginAsAdmin()
        ->navigate('/module/AreaConhecimento/edit');

    // O select de instituição não tem "Selecione": o App_Model_IedFinder devolve
    // só as instituições, e os rótulos vêm em caixa alta. A escolha é pelo id,
    // que não depende disso.
    $page = Form::cascade($page, ['instituicao' => $this->scenario->institution->getKey()]);

    $page = Form::fill($page, [
        'nome' => 'Ciências da natureza',
        'secao' => 'Lógico Matemático',
        'ordenamento_ac' => '1',
    ]);

    Form::save($page)
        ->assertPathIs('/module/AreaConhecimento/index')
        ->assertSee('Ciências da natureza');

    $area = LegacyKnowledgeArea::query()->where('nome', 'Ciências da natureza')->sole();

    expect($area->instituicao_id)->toBe($this->scenario->institution->getKey())
        ->and($area->secao)->toBe('Lógico Matemático');
});

it('edita o nome da área de conhecimento', function () {
    $area = knowledgeArea($this->scenario->institution->getKey());

    $page = $this->scenario->loginAsAdmin()
        ->navigate('/module/AreaConhecimento/edit?id=' . $area->getKey())
        ->fill('nome', 'Ciências da natureza e suas tecnologias');

    // Editar volta para o detalhe, e não para a listagem.
    Form::save($page)->assertPathIs('/module/AreaConhecimento/view');

    expect($area->fresh()->nome)->toBe('Ciências da natureza e suas tecnologias');
});

it('exclui uma área de conhecimento', function () {
    $area = knowledgeArea($this->scenario->institution->getKey());

    $page = $this->scenario->loginAsAdmin()
        ->navigate('/module/AreaConhecimento/edit?id=' . $area->getKey());

    Form::remove($page)
        ->assertPathIs('/module/AreaConhecimento/index')
        ->assertDontSee('Ciências da natureza');

    expect(LegacyKnowledgeArea::query()->find($area->getKey()))->toBeNull();
});

it('recusa gravar sem o nome da área de conhecimento', function () {
    $page = $this->scenario->loginAsAdmin()
        ->navigate('/module/AreaConhecimento/edit');

    Form::reject($page)->assertPathIs('/module/AreaConhecimento/edit');

    expect(LegacyKnowledgeArea::query()->count())->toBe(0);
});
