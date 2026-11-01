<?php

uses(UnitBaseTest::class);

test('formula valida', function () {
    $formula = 'Se / Et';
    $validator = new FormulaMedia_Validate_Formula;
    $this->assertTrue($validator->isValid($formula));
});

test('formula valida usando alias de multiplicacao', function () {
    $formula = 'Se x 0.99 / Et';
    $validator = new FormulaMedia_Validate_Formula;
    $this->assertTrue($validator->isValid($formula));
});

test('formula valida com numericos', function () {
    $formula = 'Se * 0.5 / Et';
    $validator = new FormulaMedia_Validate_Formula;
    $this->assertTrue($validator->isValid($formula));
});

test('formula invalida quando utiliza token nao permitido', function () {
    $this->expectException(Exception::class);
    $formula = 'Rc * 0.4 + Se * 0.6';
    $validator = new FormulaMedia_Validate_Formula;
    $this->assertTrue($validator->isValid($formula));
});

test('formula valida usando parenteses', function () {
    $formula = '(Rc * 0.4) + (Se * 0.6)';
    $validator = new FormulaMedia_Validate_Formula(['excludeToken' => null]);
    $this->assertTrue($validator->isValid($formula));
});

test('formula invalida por erro de sintaxe', function () {
    $this->expectException(Error::class);
    $formula = '(Rc * 0.4) + (Se * 0.6) ()';
    $validator = new FormulaMedia_Validate_Formula(['excludeToken' => null]);
    $this->assertTrue($validator->isValid($formula));
});
