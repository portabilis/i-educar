<?php

test('não duplica campos quando uma seção de filtros está vazia', function (array $basicos, array $avancados, string $secaoVazia) {
    $listagem = (new ReflectionClass(clsListagem::class))->newInstanceWithoutConstructor();
    $listagem->campoTexto('nome', 'Nome', null);
    $listagem->filtrosBasicos = $basicos;
    $listagem->filtrosAvancados = $avancados;

    $html = (new ReflectionMethod(clsListagem::class, 'renderFiltros'))->invoke($listagem);

    $inicio = strpos($html, "data-filter-section='{$secaoVazia}'");
    $fim = strpos($html, '</tbody>', $inicio);
    $conteudoVazio = substr($html, $inicio, $fim - $inicio);

    expect($conteudoVazio)->not->toContain("id='tr_nome'");
    expect(substr_count($html, "id='tr_nome'"))->toBe(1);
})->with([
    'sem campos avançados' => [['nome'], [], 'advanced'],
    'sem campos básicos' => [[], ['nome'], 'basic'],
]);

test('mantém a renderização tradicional quando a listagem não configura seções', function () {
    $listagem = (new ReflectionClass(clsListagem::class))->newInstanceWithoutConstructor();
    $listagem->campoTexto('nome', 'Nome', null);

    $html = (new ReflectionMethod(clsListagem::class, 'renderFiltros'))->invoke($listagem);

    expect($html)
        ->toContain("id='tr_nome'")
        ->not->toContain('data-filter-section');
});
