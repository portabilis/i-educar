<?php

use App\Services\NameCapitalizationService;
use Tests\TestCase;

uses(TestCase::class);

test('capitalizes simple names', function () {
    $service = new NameCapitalizationService;

    expect($service->execute('JOÃO SILVA'))->toBe('João Silva');
});

test('keeps Portuguese prepositions lowercase', function () {
    $service = new NameCapitalizationService;

    expect($service->execute('MARIA DA SILVA E OLIVEIRA'))->toBe('Maria da Silva e Oliveira');
});

test('preserves accents when capitalizing names', function () {
    $service = new NameCapitalizationService;

    expect($service->execute('ÁLVARO DE SOUZA'))->toBe('Álvaro de Souza');
});
