<?php

use App\Models\LegacySchool;
use Tests\Browser\Support\Form;
use Tests\Browser\Support\Scenario;

/**
 * Etapa 6 do roteiro: cadastrar a escola.
 *
 * O cadastro tem duas etapas. Na primeira escolhe-se a pessoa jurídica que a
 * escola representa, num campo de busca com autocomplete; só depois de
 * "Continuar" o formulário de verdade aparece.
 *
 * O caminho até gravar **ainda não está coberto**, e não por falta de mapeamento:
 * a tela remonta o próprio DOM por JS depois de carregada (o bloco de cursos
 * troca de nome, de `ref_cod_curso[0]` para `cursos[0][curso_id]`), recarrega a
 * lista de cursos por AJAX ao escolher a instituição, e tem campos obrigatórios
 * só pelo JS espalhados por outras abas — `regulamentacao` e
 * `acao_area_ambiental`. Cada um desses foi identificado e resolvido, mas o
 * conjunto ainda deixa o preenchimento instável a ponto de a rodada não terminar.
 * O que está aqui é o que se sustenta; o resto é a próxima tarefa desta tela.
 */
beforeEach(function () {
    $this->scenario = Scenario::install(adminLogin: 'adm-escola');
    $this->course = Scenario::course();
});

it('lista as escolas', function () {
    Scenario::school();

    $this->scenario->loginAsAdmin()
        ->navigate('/intranet/educar_escola_lst.php')
        ->assertSee('Escola')
        ->assertSee(Scenario::SCHOOL);
});

it('recusa gravar sem o nome da escola', function () {
    Scenario::organization();

    $page = $this->scenario->loginAsAdmin()
        ->navigate('/intranet/educar_escola_cad.php');

    $page = Form::autocomplete($page, 'pessoaj_idpes', Scenario::SCHOOL, 'pessoaj_id');
    $page = Form::action($page, 'Continuar');
    $page = Form::awaitElement($page, '#cursos select');

    Form::save($page)->assertPathIs('/intranet/educar_escola_cad.php');

    expect(LegacySchool::query()->count())->toBe(0);
});
