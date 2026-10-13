<?php

uses(UnitBaseTest::class);

beforeEach(function () {
    $this->makeRegra = function ($attrs = []) {
        $regra = new RegraAvaliacao_Model_Regra;
        foreach ($attrs as $k => $v) {
            $regra->$k = $v;
        }

        return $regra;
    };
});

test('nota maxima menor que minima', function () {
    $regra = ($this->makeRegra)([
        'notaMinimaGeral' => 6,
        'notaMaximaGeral' => 5,
    ]);

    $validator = new RegraAvaliacao_Validators_RegraAvaliacaoValidator;
    $this->assertFalse($validator->isValid($regra));
    $this->assertContains(
        'A nota máxima deve ser maior que a nota mínima.',
        $validator->getMessages()
    );
});

test('porcentagem presenca invalida', function () {
    $regra = ($this->makeRegra)([
        'porcentagemPresenca' => 150,
    ]);
    $validator = new RegraAvaliacao_Validators_RegraAvaliacaoValidator;
    $this->assertFalse($validator->isValid($regra));
    $this->assertContains(
        'A porcentagem de presença deve estar entre 0 e 100.',
        $validator->getMessages()
    );
});

test('falta maxima menor ou igual falta minima', function () {
    $regra = ($this->makeRegra)([
        'faltaMinimaGeral' => 5,
        'faltaMaximaGeral' => 5,
    ]);

    $validator = new RegraAvaliacao_Validators_RegraAvaliacaoValidator;
    $this->assertFalse($validator->isValid($regra));
    $this->assertContains(
        'A falta máxima deve ser maior que a falta mínima.',
        $validator->getMessages()
    );
});
