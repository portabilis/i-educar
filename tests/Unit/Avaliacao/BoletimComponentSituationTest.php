<?php

use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->situationFor = function (int $componentId): ?int {
        $components = new stdClass;
        $components->componentesCurriculares = [
            10 => (object) ['situacao' => App_Model_MatriculaSituacao::APROVADO],
        ];

        $boletim = $this->createPartialMock(Avaliacao_Service_Boletim::class, ['getSituacaoComponentesCurriculares']);
        $boletim->method('getSituacaoComponentesCurriculares')->willReturn($components);

        $method = new ReflectionMethod(Avaliacao_Service_Boletim::class, 'getCurricularComponentSituation');

        return $method->invoke($boletim, $componentId);
    };
});

test('returns the situation of a component of the registration', function () {
    $this->assertSame(App_Model_MatriculaSituacao::APROVADO, ($this->situationFor)(10));
});

test('returns null for a component outside the registration', function () {
    $this->assertNull(($this->situationFor)(21));
});
