<?php
uses(UnitBaseTest::class);


function regraBasisPath(array $substituir = [], array $ausentes = []): array
{
    $baseline = [
        'notaMinimaGeral' => 0,
        'notaMaximaGeral' => 10,
        'porcentagemPresenca' => 75,
        'faltaMinimaGeral' => 0,
        'faltaMaximaGeral' => 20,
    ];

    return array_diff_key(array_merge($baseline, $substituir), array_flip($ausentes));
}

beforeEach(function () {
    $this->mensagens = [
        'nota' => 'A nota máxima deve ser maior que a nota mínima.',
        'presenca' => 'A porcentagem de presença deve estar entre 0 e 100.',
        'falta' => 'A falta máxima deve ser maior que a falta mínima.',
    ];
});

test('cada caminho básico produz o retorno e as mensagens esperados', function (array $atributos, array $esperadas) {
    $regra = new RegraAvaliacao_Model_Regra;

    foreach ($atributos as $campo => $valor) {
        $regra->$campo = $valor;
    }

    $validator = new RegraAvaliacao_Validators_RegraAvaliacaoValidator;
    $valida = $validator->isValid($regra);

    $mensagensEsperadas = array_map(fn (string $chave) => $this->mensagens[$chave], $esperadas);

    expect($validator->getMessages())->toBe($mensagensEsperadas)
        ->and($valida)->toBe($esperadas === []);
})->with([
    'P0 baseline: tudo informado e válido' => [
        regraBasisPath(),
        [],
    ],
    'P1 a1=F: nota mínima ausente' => [
        regraBasisPath(ausentes: ['notaMinimaGeral']),
        [],
    ],
    'P2 a2=F: nota máxima ausente' => [
        regraBasisPath(ausentes: ['notaMaximaGeral']),
        [],
    ],
    'P3 b=V: nota máxima (0) menor que a mínima (10)' => [
        regraBasisPath(['notaMinimaGeral' => 10, 'notaMaximaGeral' => 0]),
        ['nota'],
    ],
    'P4 g=F: presença ausente' => [
        regraBasisPath(ausentes: ['porcentagemPresenca']),
        [],
    ],
    'P5 c1=V: presença -5 (c2 não é avaliado)' => [
        regraBasisPath(['porcentagemPresenca' => -5]),
        ['presenca'],
    ],
    'P6 c1=F, c2=V: presença 150' => [
        regraBasisPath(['porcentagemPresenca' => 150]),
        ['presenca'],
    ],
    'P7 e1=F: falta mínima ausente' => [
        regraBasisPath(ausentes: ['faltaMinimaGeral']),
        [],
    ],
    'P8 e2=F: falta máxima ausente' => [
        regraBasisPath(ausentes: ['faltaMaximaGeral']),
        [],
    ],
    'P9 f=V: falta máxima (0) menor que a mínima (20)' => [
        regraBasisPath(['faltaMinimaGeral' => 20, 'faltaMaximaGeral' => 0]),
        ['falta'],
    ],
]);
