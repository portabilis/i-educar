<?php

use iEducar\Modules\Educacenso\Validator\IdadePermitidaValidator;
use Tests\TestCase;

uses(TestCase::class);

test('idade nula nao bloqueia', function () {
    $validator = new IdadePermitidaValidator(null, [7, 50], 'a etapa de ensino 5º Ano');

    $this->assertTrue($validator->isValid());
});

test('faixa nula nao bloqueia', function () {
    $validator = new IdadePermitidaValidator(6, null, 'a etapa de ensino 5º Ano');

    $this->assertTrue($validator->isValid());
});

test('idade dentro da faixa valida', function () {
    $validator = new IdadePermitidaValidator(10, [7, 50], 'a etapa de ensino 5º Ano');

    $this->assertTrue($validator->isValid());
});

test('idade igual ao minimo valida', function () {
    $validator = new IdadePermitidaValidator(7, [7, 50], 'a etapa de ensino 5º Ano');

    $this->assertTrue($validator->isValid());
});

test('idade igual ao maximo valida', function () {
    $validator = new IdadePermitidaValidator(50, [7, 50], 'a etapa de ensino 5º Ano');

    $this->assertTrue($validator->isValid());
});

test('idade abaixo do minimo invalida', function () {
    // caso do aluno Rhyan: 6 anos numa etapa que exige no mínimo 7
    $validator = new IdadePermitidaValidator(6, [7, 50], 'a etapa de ensino 5º Ano');

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('6 anos', $validator->getMessage());
    $this->assertStringContainsString('7 a 50', $validator->getMessage());
});

test('idade acima do maximo invalida', function () {
    $validator = new IdadePermitidaValidator(51, [7, 50], 'a etapa de ensino 5º Ano');

    $this->assertFalse($validator->isValid());
});

test('mensagem vazia quando valido', function () {
    $validator = new IdadePermitidaValidator(10, [7, 50], 'a etapa de ensino 5º Ano');
    $validator->isValid();

    $this->assertSame('', $validator->getMessage());
});

test('gestor abaixo do minimo invalido', function () {
    // gestor de 17 anos, faixa da função de 18 a 95
    $validator = new IdadePermitidaValidator(17, [18, 95], 'a função de gestor(a) escolar (registro 40)');

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('18 a 95', $validator->getMessage());
});

test('profissional abaixo do minimo invalido', function () {
    // profissional de 13 anos, faixa da função de 14 a 95
    $validator = new IdadePermitidaValidator(13, [14, 95], 'a função de profissional escolar em sala de aula (registro 50)');

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('14 a 95', $validator->getMessage());
});

test('aluno em turma prisional abaixo do minimo invalido', function () {
    // aluno de 15 anos numa turma em unidade prisional, faixa de 18 a 94
    $validator = new IdadePermitidaValidator(15, [18, 94], 'turmas em unidade prisional');

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('18 a 94', $validator->getMessage());
});
