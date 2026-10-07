<?php

namespace Tests\Unit\Avaliacao;

use Avaliacao_Service_Boletim;
use ReflectionMethod;
use Tests\TestCase;

class BoletimSpecificRecoveryTest extends TestCase
{
    private function recoveryRule(array $stages): object
    {
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
    }

    private function calculate(?object $recoveryScore, array $data): array
    {
        $boletim = $this->createPartialMock(Avaliacao_Service_Boletim::class, ['getRegrasRecuperacao', 'getNotaComponente']);
        $boletim->method('getRegrasRecuperacao')->willReturn([$this->recoveryRule([3, 4])]);
        $boletim->method('getNotaComponente')->willReturn($recoveryScore);

        $method = new ReflectionMethod(Avaliacao_Service_Boletim::class, '_calculateNotasRecuperacoesEspecificas');

        return $method->invoke($boletim, 1, $data);
    }

    public function test_stage_without_score_counts_as_zero_when_there_is_no_recovery_score()
    {
        $data = $this->calculate(null, ['E3' => 8]);

        $this->assertEquals(8, $data['Se']);
        $this->assertEquals(4, $data['RSPM1']);
        $this->assertEquals(8, $data['RSPS1']);
    }

    public function test_stage_without_score_counts_as_zero_when_there_is_a_recovery_score()
    {
        $recoveryScore = (object) ['notaRecuperacaoEspecifica' => 6];

        $data = $this->calculate($recoveryScore, ['E3' => 8]);

        $this->assertEquals(6, $data['RSP1']);
        $this->assertEquals(6, $data['Se']);
        $this->assertEquals(5, $data['RSPM1']);
        $this->assertEquals(6, $data['RSPS1']);
    }
}
