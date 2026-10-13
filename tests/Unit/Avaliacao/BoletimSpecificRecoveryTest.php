<?php

use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->recoveryRule = function (array $stages): object {
        return new class($stages)
        {
            public $substituiMenorNota = false;

            public function __construct(private array $stages) {}

            public function getEtapas(): array
            {
                return $this->stages;
            }

            public function getLastEtapa(): int
            {
                return max($this->stages);
            }
        };
    };

    $this->calculate = function (?object $recoveryScore, array $data): array {
        $boletim = $this->createPartialMock(Avaliacao_Service_Boletim::class, ['getRegrasRecuperacao', 'getNotaComponente']);
        $boletim->method('getRegrasRecuperacao')->willReturn([($this->recoveryRule)([3, 4])]);
        $boletim->method('getNotaComponente')->willReturn($recoveryScore);

        $method = new ReflectionMethod(Avaliacao_Service_Boletim::class, '_calculateNotasRecuperacoesEspecificas');

        return $method->invoke($boletim, 1, $data);
    };
});

test('stage without score counts as zero when there is no recovery score', function () {
    $data = ($this->calculate)(null, ['E3' => 8]);

    $this->assertEquals(8, $data['Se']);
    $this->assertEquals(4, $data['RSPM1']);
    $this->assertEquals(8, $data['RSPS1']);
});

test('stage without score counts as zero when there is a recovery score', function () {
    $recoveryScore = (object) ['notaRecuperacaoEspecifica' => 6];

    $data = ($this->calculate)($recoveryScore, ['E3' => 8]);

    $this->assertEquals(6, $data['RSP1']);
    $this->assertEquals(6, $data['Se']);
    $this->assertEquals(5, $data['RSPM1']);
    $this->assertEquals(6, $data['RSPS1']);
});
