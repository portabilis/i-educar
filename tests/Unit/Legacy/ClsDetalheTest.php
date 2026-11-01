<?php

use iEducar\Support\Navigation\Breadcrumb;
use Tests\TestCase;

uses(TestCase::class);

function renderizaDetalhe(clsDetalhe $detalhe): string
{
    // As telas legadas rodam sem avisos do PHP: o LegacyController zera o error_reporting.
    $level = error_reporting(0);

    try {
        return $detalhe->RenderHTML();
    } finally {
        error_reporting($level);
    }
}

test('o Gerar padrão não monta nada', function () {
    expect((new clsDetalhe)->Gerar())->toBeFalse();
});

test('sem detalhes avisa que não há informação, com o título e o breadcrumb da tela', function (Closure $tela) {
    $detalhe = $tela();
    $detalhe->locale = 'Escolas';

    expect(renderizaDetalhe($detalhe))->toContain('Não há informação a ser apresentada.')
        ->and(app(Breadcrumb::class)->getLegacy())->toBe('Escolas')
        ->and(view()->shared('title'))->toBe('Detalhe da escola');
})->with([
    '_title' => [fn () => tap(new clsDetalhe, fn ($detalhe) => $detalhe->_title = 'Detalhe da escola')],
    '_titulo' => [fn () => new class extends clsDetalhe
    {
        protected $_titulo = 'Detalhe da escola';
    }],
]);

test('o separador sem texto vira uma linha em branco', function () {
    $detalhe = new clsDetalhe;
    $detalhe->addDetalhe(['-', '']);
    $detalhe->addDetalhe(['Nome', 'Ana']);

    expect(renderizaDetalhe($detalhe))
        ->toContain("<span class='form'><b>&nbsp;</b></span>")
        ->toContain('Ana');
});
