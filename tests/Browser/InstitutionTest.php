<?php

use Tests\Browser\Support\Form;
use Tests\Browser\Support\Scenario;

/**
 * Etapa 1 do roteiro de implantação: ajustar a instituição.
 *
 * A instituição não é criável pela interface — o Novo() de
 * educar_instituicao_cad.php só redireciona para a listagem, sem gravar, e a
 * listagem não oferece botão "Novo". Numa instalação real ela vem da migration
 * database/migrations/data/2020_01_01_190028_default_data_pmieducar_instituicao.php,
 * então a implantação começa editando a que já existe.
 */
beforeEach(function () {
    $this->scenario = Scenario::install(adminLogin: 'adm-inst');
});

it('lista a instituição e abre o detalhe', function () {
    $this->scenario->loginAsAdmin()
        ->navigate('/intranet/educar_instituicao_lst.php')
        ->assertSee('Instituição - Listagem')
        ->assertSee('Prefeitura Municipal de Modelópolis')
        ->assertSee('Total de registros: 1')
        ->click('Prefeitura Municipal de Modelópolis')
        ->assertPathIs('/intranet/educar_instituicao_det.php')
        ->assertSee('Instituição - Detalhe')
        ->assertSee('Rua João Paulo Segundo')
        ->assertSee('Secretaria de Educação e Cultura')
        ->assertNoJavaScriptErrors();
});

it('não oferece cadastro de nova instituição', function () {
    // Documenta a regra: a implantação parte da instituição criada na
    // instalação. Se um botão "Novo" aparecer aqui, o comportamento mudou.
    $this->scenario->loginAsAdmin()
        ->navigate('/intranet/educar_instituicao_lst.php')
        ->assertSee('Instituição - Listagem')
        ->assertDontSee('Novo');
});

it('filtra a listagem pelo nome', function () {
    $page = $this->scenario->loginAsAdmin()
        ->navigate('/intranet/educar_instituicao_lst.php')
        ->fill('nm_instituicao', 'Modelópolis');

    Form::search($page)
        ->assertSee('Prefeitura Municipal de Modelópolis')
        ->assertSee('Total de registros: 1');

    $page = $page->navigate('/intranet/educar_instituicao_lst.php')
        ->fill('nm_instituicao', 'Instituição que não existe');

    Form::search($page)->assertDontSee('Prefeitura Municipal de Modelópolis');
});

it('edita os dados da instituição', function () {
    $institution = $this->scenario->institution;

    $page = $this->scenario->loginAsAdmin()
        ->navigate('/intranet/educar_instituicao_lst.php')
        ->click('Prefeitura Municipal de Modelópolis')
        ->click('Editar')
        ->assertPathIs('/intranet/educar_instituicao_cad.php');

    $page = Form::fill($page, [
        '#nm_instituicao' => 'Prefeitura Municipal de Nova Modelópolis',
        '#nm_responsavel' => 'Secretaria Municipal de Educação',
        '#bairro' => 'Centro Histórico',
    ]);

    Form::save($page)->assertPathIs('/intranet/educar_instituicao_lst.php');

    expect($institution->fresh())
        ->nm_instituicao->toBe('Prefeitura Municipal de Nova Modelópolis')
        ->nm_responsavel->toBe('Secretaria Municipal de Educação')
        ->bairro->toBe('Centro Histórico');
});

it('recusa gravar sem o nome da instituição', function () {
    $institution = $this->scenario->institution;

    $page = $this->scenario->loginAsAdmin()
        ->navigate('/intranet/educar_instituicao_cad.php?cod_instituicao=' . $institution->getKey())
        ->fill('#nm_instituicao', '');

    // A validação do clsCadastro é JS e barra o submit: a página não navega.
    Form::save($page)->assertPathIs('/intranet/educar_instituicao_cad.php');

    expect($institution->fresh()->nm_instituicao)->toBe('Prefeitura Municipal de Modelópolis');
});
