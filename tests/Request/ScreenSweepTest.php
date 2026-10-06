<?php

use Tests\Browser\Support\Scenario;
use Tests\Request\Support\Screens;

/**
 * Varredura: toda tela de listagem e de cadastro do legado abre sem estourar.
 *
 * É a rede mais larga e mais barata que existe para esta base — e hoje não há
 * nenhuma. A asserção é deliberadamente fraca: a tela pode responder 200, pode
 * redirecionar por permissão ou por pré-requisito ausente, mas **não pode
 * devolver 5xx**. Erro de servidor numa tela que abre em branco é sempre
 * defeito, nunca configuração.
 *
 * O que isso guarda é o que nenhum teste de caminho feliz guarda: uma função
 * removida, uma assinatura mudada, um `use` esquecido numa tela que ninguém
 * visita há anos.
 */
beforeEach(function () {
    $this->scenario = Scenario::install(adminLogin: 'req-varre');

    $this->actingAs($this->scenario->admin);
});

it('abre sem erro de servidor', function (string $rota) {
    $resposta = $this->get($rota);

    // A mensagem carrega o começo do corpo da resposta: numa varredura de
    // noventa e tantas telas, saber *qual* erro a tela deu vale mais do que
    // saber que ela deu algum.
    $this->assertLessThan(500, $resposta->status(), sprintf(
        '%s respondeu %d: %s',
        $rota,
        $resposta->status(),
        mb_substr(trim(preg_replace('/\s+/', ' ', strip_tags($resposta->getContent()))), 0, 300),
    ));
})->with(Screens::all());

/**
 * As telas que a varredura deixa de fora por já estarem quebradas.
 *
 * Este teste é o que impede a lista de apodrecer: no dia em que alguém
 * corrigir uma delas, ele falha, e a falha é a deixa para tirar a tela do
 * Screens::BROKEN e devolvê-la à varredura.
 */
it('segue quebrada', function (string $rota) {
    expect($this->get($rota)->status())->toBe(500);
})->with(Screens::broken());
