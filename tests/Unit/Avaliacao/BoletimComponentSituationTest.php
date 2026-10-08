<?php

namespace Tests\Unit\Avaliacao;

use App_Model_MatriculaSituacao;
use Avaliacao_Service_Boletim;
use ReflectionMethod;
use stdClass;
use Tests\TestCase;

class BoletimComponentSituationTest extends TestCase
{
    private function situationFor(int $componentId): ?int
    {
        $components = new stdClass;
        $components->componentesCurriculares = [
            10 => (object) ['situacao' => App_Model_MatriculaSituacao::APROVADO],
        ];

        $boletim = $this->createPartialMock(Avaliacao_Service_Boletim::class, ['getSituacaoComponentesCurriculares']);
        $boletim->method('getSituacaoComponentesCurriculares')->willReturn($components);

        $method = new ReflectionMethod(Avaliacao_Service_Boletim::class, 'getCurricularComponentSituation');

        return $method->invoke($boletim, $componentId);
    }

    public function test_returns_the_situation_of_a_component_of_the_registration()
    {
        $this->assertSame(App_Model_MatriculaSituacao::APROVADO, $this->situationFor(10));
    }

    public function test_returns_null_for_a_component_outside_the_registration()
    {
        $this->assertNull($this->situationFor(21));
    }
}
