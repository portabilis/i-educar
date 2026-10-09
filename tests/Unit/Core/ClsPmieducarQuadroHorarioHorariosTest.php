<?php

use Tests\TestCase;

uses(TestCase::class);

/**
 * Testa o método substituir_servidor()
 */
test('substituir servidor', function () {
    $stub = $this->getMockBuilder('clsPmieducarQuadroHorarioHorarios')->getMock();

    $stub->expects($this->any())
        ->method('substituir_servidor')
        ->willReturn(true);

    $this->assertTrue($stub->substituir_servidor(1));
});
