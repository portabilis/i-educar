<?php

class App_Model_MatriculaTest extends UnitBaseTest
{
    public function testAtualizaMatricula()
    {
        $matricula = $this->getCleanMock('clsPmieducarMatricula');
        $matricula->expects($this->once())
            ->method('edita')
            ->will($this->returnValue(true));

        // Guarda no repositório estático de classes
        CoreExt_Entity::addClassToStorage(
            'clsPmieducarMatricula',
            $matricula,
            null,
            true
        );

        App_Model_Matricula::atualizaMatricula(1, 1, true);
    }
}
