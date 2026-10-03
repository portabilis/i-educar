<?php

use App\Models\LegacyUserType;
use Tests\Browser\Support\Form;
use Tests\Browser\Support\Menus;
use Tests\Browser\Support\Scenario;

/**
 * Etapa 2 do roteiro: criar tipos de usuário e dar permissões.
 *
 * Esta é a única tela do roteiro que não é legada: o cadastro foi migrado para o
 * AccessLevelController (/usuarios/tipos/novo) com uma interface em Vue 2, e a
 * listagem continua sendo educar_tipo_usuario_lst.php servida em /usuarios/tipos.
 *
 * As permissões vão para pmieducar.menu_tipo_usuario, uma linha por item de menu,
 * com as colunas visualiza/cadastra/exclui. O formulário manda
 * `processes[<menus.id>]` com 0 a 3, e o controller desdobra isso nas três
 * colunas (3 marca as três, 2 marca visualiza e cadastra, 1 só visualiza).
 */
beforeEach(function () {
    $this->scenario = Scenario::install(adminLogin: 'adm-tipo');
});

it('lista os tipos de usuário', function () {
    $this->scenario->loginAsAdmin()
        ->navigate('/usuarios/tipos')
        ->assertSee('Tipo Usuário')
        ->assertSee('Nível')
        ->assertNoJavaScriptErrors();
});

it('cadastra um tipo de usuário com permissão total', function () {
    $page = $this->scenario->loginAsAdmin()->navigate('/usuarios/tipos/novo');

    $page = Form::fill($page, ['name' => 'Secretaria Escolar', 'description' => 'Secretaria da escola'])
        ->select('level', 'Escola')
        // Botão de atalho que marca a opção para todos os itens de menu.
        ->click('Exclui')
        ->click('Salvar')
        ->waitForEvent('networkidle')
        ->assertPathIs('/usuarios/tipos');

    $type = LegacyUserType::query()->where('nm_tipo', 'Secretaria Escolar')->sole();

    expect($type->nivel)->toBe(App_Model_NivelTipoUsuario::ESCOLA);

    // "Exclui" é o nível mais alto: marca as três colunas de todos os itens.
    expect($type->menus()->count())->toBe(Menus::count());
    expect($type->menus()->wherePivot('exclui', 1)->count())->toBe(Menus::count());
});

it('cadastra um tipo de usuário que só pode visualizar', function () {
    $page = $this->scenario->loginAsAdmin()->navigate('/usuarios/tipos/novo');

    Form::fill($page, ['name' => 'Consulta', 'description' => 'Somente leitura'])
        ->select('level', 'Institucional')
        ->click('Visualiza')
        ->click('Salvar')
        ->waitForEvent('networkidle')
        ->assertPathIs('/usuarios/tipos');

    $type = LegacyUserType::query()->where('nm_tipo', 'Consulta')->sole();

    expect($type->menus()->wherePivot('visualiza', 1)->count())->toBe(Menus::count());
    expect($type->menus()->wherePivot('cadastra', 1)->count())->toBe(0);
    expect($type->menus()->wherePivot('exclui', 1)->count())->toBe(0);
});

it('edita o nome de um tipo de usuário', function () {
    $type = Scenario::userType('Coordenação', processes: [Menus::SCHOOLS]);

    $page = $this->scenario->loginAsAdmin()
        ->navigate('/usuarios/tipos/' . $type->getKey())
        ->fill('name', 'Coordenação Pedagógica');

    $page->click('Salvar')->waitForEvent('networkidle')->assertPathIs('/usuarios/tipos');

    expect($type->fresh()->nm_tipo)->toBe('Coordenação Pedagógica');
});
