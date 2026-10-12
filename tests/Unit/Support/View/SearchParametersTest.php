<?php

use App\Support\View\SearchParameters;

test('guarda os campos por posição e devolve um campo ou todos', function () {
    $parameters = new SearchParameters;
    $parameters->setCampoNome(0, 'nome');
    $parameters->setCampoTipoTexto(0);
    $parameters->setCampoIndice(0, 'idpes');
    $parameters->setCampoValor(0, 'Ana');
    $parameters->setCampoNome(1, 'situacao');
    $parameters->setCampoTipoSelect(1);
    $parameters->setCampoIndice(1, 'ativo');
    $parameters->setCampoValor(1, '1');

    expect($parameters->getCampoNome(1))->toBe('situacao')
        ->and($parameters->getCampoTipo())->toBe(['text', 'select'])
        ->and($parameters->getCampoIndice())->toBe(['idpes', 'ativo'])
        ->and($parameters->getCampoValor())->toBe(['Ana', '1']);
});

test('guarda as opções da pesquisa de pessoa', function () {
    $parameters = new SearchParameters;
    $parameters->setPessoaNovo('S');
    $parameters->setPessoaCampo('ref_idpes');
    $parameters->setCodSistema(7);
    $parameters->setPessoaCPF('N');

    expect($parameters->getPessoaNovo())->toBe('S')
        ->and($parameters->getPessoaCampo())->toBe('ref_idpes')
        ->and($parameters->getCodSistema())->toBe(7)
        ->and($parameters->getPessoaCPF())->toBe('N');
});
