<?php

use iEducar\Modules\Educacenso\Deficiencia\DeficienciaMultiplaProfessor;
use iEducar\Modules\Educacenso\Deficiencia\ValueDeficienciaMultipla;
use Tests\TestCase;

uses(TestCase::class);

test('lista os pares de deficiencias que formam deficiencia multipla', function () {
    $combinacoes = (new DeficienciaMultiplaProfessor)->getCombinacoes();

    $this->assertCount(14, $combinacoes);
    $this->assertContains(['19', '24'], $combinacoes);
    $this->assertContains(['24', '25'], $combinacoes);
});

test('sem deficiencias o valor fica vazio', function () {
    $this->assertNull((new ValueDeficienciaMultipla(new DeficienciaMultiplaProfessor, []))->getValue());
});

test('um par da lista e deficiencia multipla', function () {
    $this->assertSame(1, (new ValueDeficienciaMultipla(new DeficienciaMultiplaProfessor, ['20', '25']))->getValue());
});

test('deficiencias que nao formam par nao sao deficiencia multipla', function () {
    $this->assertSame(0, (new ValueDeficienciaMultipla(new DeficienciaMultiplaProfessor, ['19', '20']))->getValue());
});
