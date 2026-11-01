<?php

use Tests\Browser\Support\Scenario;

/**
 * Etapa 8 do roteiro: vincular a série à escola.
 *
 * É o cadastro que liga escola, curso e série e define em quais anos letivos
 * aquela série é oferecida. Sem ele não há turma nem matrícula.
 *
 * O cadastro **ainda não está coberto**: a tela encadeia quatro selects por AJAX
 * (instituição, escola, curso, série) e um multi-select de anos letivos, e a
 * cascata não completa dentro do tempo que o plugin do Pest dá para cada ação —
 * o teste fica pendurado em vez de falhar, que é o modo de falha conhecido
 * quando um seletor não casa. O que está aqui é a listagem, que se sustenta.
 */
beforeEach(function () {
    $this->scenario = Scenario::install(adminLogin: 'adm-escserie');
    $this->school = Scenario::school(Scenario::course());
});

it('lista as séries da escola', function () {
    $this->scenario->loginAsAdmin()
        ->navigate('/intranet/educar_escola_serie_lst.php')
        ->assertSee('Série')
        ->assertSee('Escola');
});
