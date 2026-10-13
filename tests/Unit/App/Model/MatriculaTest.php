<?php

uses(UnitBaseTest::class);

test('atualiza matricula', function () {
    $matricula = $this->getCleanMock('clsPmieducarMatricula');
    $matricula->expects($this->once())
        ->method('edita')
        ->willReturn(true);

    // Guarda no repositório estático de classes
    CoreExt_Entity::addClassToStorage(
        'clsPmieducarMatricula',
        $matricula,
        null,
        true
    );

    App_Model_Matricula::atualizaMatricula(1, 1, true);
});
