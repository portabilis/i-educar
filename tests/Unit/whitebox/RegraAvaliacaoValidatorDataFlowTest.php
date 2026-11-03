<?php
uses(UnitBaseTest::class);

beforeEach(function () {
    $this->mensagens = [
        'nota' => 'A nota máxima deve ser maior que a nota mínima.',
        'presenca' => 'A porcentagem de presença deve estar entre 0 e 100.',
        'falta' => 'A falta máxima deve ser maior que a falta mínima.',
    ];

    $this->makeRegra = function (array $atributos): RegraAvaliacao_Model_Regra {
        $regra = new RegraAvaliacao_Model_Regra;

        foreach ($atributos as $campo => $valor) {
            $regra->$campo = $valor;
        }

        return $regra;
    };

    // Atributos que sozinhos produzem cada mensagem.
    $this->invalida = [
        'nota' => ['notaMinimaGeral' => 6, 'notaMaximaGeral' => 5],
        'presenca' => ['porcentagemPresenca' => 150],
        'falta' => ['faltaMinimaGeral' => 5, 'faltaMaximaGeral' => 4],
    ];

    $this->textos = fn (array $chaves): array => array_map(fn (string $chave) => $this->mensagens[$chave], $chaves);
});

test('CT-E1 d0→u2: getMessages() numa instância nova devolve lista vazia', function () {
    $validator = new RegraAvaliacao_Validators_RegraAvaliacaoValidator;

    expect($validator->getMessages())->toBe([]);
});

test('CT-E2 d1→u1 e d1→u2: regra válida devolve true e nenhuma mensagem', function () {
    $validator = new RegraAvaliacao_Validators_RegraAvaliacaoValidator;

    $valida = $validator->isValid(($this->makeRegra)([
        'notaMinimaGeral' => 0,
        'notaMaximaGeral' => 10,
        'porcentagemPresenca' => 75,
        'faltaMinimaGeral' => 0,
        'faltaMaximaGeral' => 20,
    ]));

    expect($valida)->toBeTrue()
        ->and($validator->getMessages())->toBe([]);
});

test('CT-E3/E4/E5 d2, d3 e d4 chegam a u1 e u2 sem definição no meio', function (string $chave) {
    $validator = new RegraAvaliacao_Validators_RegraAvaliacaoValidator;

    $valida = $validator->isValid(($this->makeRegra)($this->invalida[$chave]));

    expect($valida)->toBeFalse()
        ->and($validator->getMessages())->toBe(($this->textos)([$chave]));
})->with([
    'CT-E3 d2 (nota)' => ['nota'],
    'CT-E4 d3 (presença)' => ['presenca'],
    'CT-E5 d4 (falta)' => ['falta'],
]);

test('CT-E6/E7/E8 a definição dp de $p alcança c1 e c2 pelos dois desvios', function (float $p, array $esperadas) {
    $validator = new RegraAvaliacao_Validators_RegraAvaliacaoValidator;

    $valida = $validator->isValid(($this->makeRegra)(['porcentagemPresenca' => $p]));

    expect($validator->getMessages())->toBe(($this->textos)($esperadas))
        ->and($valida)->toBe($esperadas === []);
})->with([
    'CT-E6 (dp,c1=V) p=-1: c1 decide, c2 não é avaliado' => [-1.0, ['presenca']],
    'CT-E7 (dp,c1=F)(dp,c2=F) p=50: os dois usos dão falso' => [50.0, []],
    'CT-E8 (dp,c2=V) p=101: c1 falso, c2 decide' => [101.0, ['presenca']],
]);

test('CT-E9 d2, d3 e d4 acumulam na mesma chamada, na ordem do código', function () {
    $validator = new RegraAvaliacao_Validators_RegraAvaliacaoValidator;

    $regra = ($this->makeRegra)(array_merge(
        $this->invalida['falta'],
        $this->invalida['presenca'],
        $this->invalida['nota'],
    ));

    expect($validator->isValid($regra))->toBeFalse()
        ->and($validator->getMessages())->toBe(($this->textos)(['nota', 'presenca', 'falta']));
});

test('CT-E10/E11/E12 sequência de chamadas na mesma instância: cada uma vê só as próprias mensagens', function (array $sequencia) {
    $validator = new RegraAvaliacao_Validators_RegraAvaliacaoValidator;

    foreach ($sequencia as [$chavesDeEntrada, $esperadas]) {
        $atributos = [];
        foreach ($chavesDeEntrada as $chave) {
            $atributos = array_merge($atributos, $this->invalida[$chave]);
        }

        $valida = $validator->isValid(($this->makeRegra)($atributos));

        expect($validator->getMessages())->toBe(($this->textos)($esperadas))
            ->and($valida)->toBe($esperadas === []);
    }
})->with([
    'CT-E10 inválida → válida' => [[
        [['nota'], ['nota']],
        [[], []],
    ]],
    'CT-E11 mesma inválida duas vezes' => [[
        [['presenca'], ['presenca']],
        [['presenca'], ['presenca']],
    ]],
    'CT-E12 três mensagens → uma' => [[
        [['nota', 'presenca', 'falta'], ['nota', 'presenca', 'falta']],
        [['falta'], ['falta']],
    ]],
]);

test('CT-E13 d0 é por instância: mensagens de um validador não aparecem em outro', function () {
    $primeiro = new RegraAvaliacao_Validators_RegraAvaliacaoValidator;
    $segundo = new RegraAvaliacao_Validators_RegraAvaliacaoValidator;

    $primeiro->isValid(($this->makeRegra)($this->invalida['nota']));

    expect($primeiro->getMessages())->toBe(($this->textos)(['nota']))
        ->and($segundo->getMessages())->toBe([]);
});

test('CT-E14 u2 apenas usa: ler getMessages() várias vezes não altera o estado', function () {
    $validator = new RegraAvaliacao_Validators_RegraAvaliacaoValidator;
    $validator->isValid(($this->makeRegra)($this->invalida['falta']));

    $primeiraLeitura = $validator->getMessages();
    $segundaLeitura = $validator->getMessages();

    expect($segundaLeitura)->toBe($primeiraLeitura)
        ->and($primeiraLeitura)->toBe(($this->textos)(['falta']));
});
