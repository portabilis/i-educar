<?php

uses(Avaliacao_Service_FaltaCommon::class);

beforeEach(function () {
    $this->configuraRegra(['tipoPresenca' => RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE]);

    $this->getFaltaTestInstanciaDeFaltaERegistradaApenasUmaVezNoBoletim = function () {
        return new Avaliacao_Model_FaltaComponente([
            'componenteCurricular' => 1,
            'quantidade' => 5,
        ]);
    };

    $this->getFaltaTestAdicionaFaltaNoBoletim = function () {
        // Método _hydrateComponentes em IedFinder foi alterado. Terá que ser escrito um novo teste
        $this->markTestSkipped();

        return new Avaliacao_Model_FaltaComponente([
            'componenteCurricular' => 1,
            'quantidade' => 10,
        ]);
    };

    $this->testAdicionaFaltaNoBoletimVerificaValidadores = function (Avaliacao_Model_FaltaAbstract $falta) {
        $this->assertEquals(1, $falta->get('componenteCurricular'));
        $this->assertEquals(1, $falta->etapa);
        $this->assertEquals(10, $falta->quantidade);

        $validators = $falta->getValidatorCollection();
        $this->assertInstanceOf('CoreExt_Validate_Choice', $validators['componenteCurricular']);
        $this->assertInstanceOf('CoreExt_Validate_Choice', $validators['etapa']);

        // Opções dos validadores

        // Componentes curriculares existentes para o aluno
        $expected = $this->_getConfigOptions('componenteCurricular');
        $dispensas = $this->_getDispensaDisciplina();
        foreach ($dispensas as $dispensa) {
            unset($expected[$dispensa['ref_cod_disciplina']]);
        }

        $actual = $validators['componenteCurricular']->getOption('choices');
        $this->assertEquals(
            array_keys($expected),
            array_values($actual)
        );

        // Etapas possíveis para o lançamento de nota
        $this->assertEquals(
            array_merge(range(1, count($this->_getConfigOptions('anoLetivoModulo'))), ['Rc']),
            $validators['etapa']->getOption('choices')
        );
    };
});

Avaliacao_Service_FaltaCommon::registerFaltaTests();

/**
 * Testa o service adicionando faltas de apenas um componente curricular,
 * para todas as etapas regulares (1 a 4).
 */
test('salvar faltas de um componente curricular no boletim', function () {
    $this->markTestSkipped();

    $faltaAluno = $this->_getConfigOption('faltaAluno', 'instance');

    $faltas = [
        new Avaliacao_Model_FaltaComponente([
            'componenteCurricular' => 1,
            'quantidade' => 7,
            'etapa' => 1,
        ]),
        new Avaliacao_Model_FaltaComponente([
            'componenteCurricular' => 1,
            'quantidade' => 9,
            'etapa' => 2,
        ]),
        new Avaliacao_Model_FaltaComponente([
            'componenteCurricular' => 1,
            'quantidade' => 8,
            'etapa' => 3,
        ]),
        new Avaliacao_Model_FaltaComponente([
            'componenteCurricular' => 1,
            'quantidade' => 8,
            'etapa' => 4,
        ]),
    ];

    // Configura mock para Avaliacao_Model_FaltaComponenteDataMapper
    $mock = $this->getCleanMock('Avaliacao_Model_FaltaComponenteDataMapper');

    $mock->expects($this->at(0))
        ->method('findAll')
        ->with([], ['faltaAluno' => $faltaAluno->id], ['etapa' => 'ASC'])
        ->willReturn([]);

    $mock->expects($this->at(1))
        ->method('save')
        ->with($faltas[0])
        ->willReturn(true);

    $mock->expects($this->at(2))
        ->method('save')
        ->with($faltas[1])
        ->willReturn(true);

    $mock->expects($this->at(3))
        ->method('save')
        ->with($faltas[2])
        ->willReturn(true);

    $mock->expects($this->at(4))
        ->method('save')
        ->with($faltas[3])
        ->willReturn(true);

    $this->_setFaltaAbstractDataMapperMock($mock);

    $service = $this->_getServiceInstance();

    $service->addFaltas($faltas);
    $service->saveFaltas();
});

/**
 * Testa o service adicionando novas faltas para um componente curricular,
 * que inclusive já tem a falta lançada para a segunda etapa.
 */
test('salvas faltas de um componente com etapas lancadas', function () {
    $this->markTestSkipped();

    $faltaAluno = $this->_getConfigOption('faltaAluno', 'instance');

    $faltas = [
        new Avaliacao_Model_FaltaComponente([
            'componenteCurricular' => 1,
            'quantidade' => 7,
            'etapa' => 2,
        ]),
        new Avaliacao_Model_FaltaComponente([
            'componenteCurricular' => 1,
            'quantidade' => 9,
            'etapa' => 3,
        ]),
    ];

    $faltasPersistidas = [
        new Avaliacao_Model_FaltaComponente([
            'id' => 1,
            'faltaAluno' => $faltaAluno->id,
            'componenteCurricular' => 1,
            'quantidade' => 8,
            'etapa' => 1,
        ]),
        new Avaliacao_Model_FaltaComponente([
            'id' => 2,
            'faltaAluno' => $faltaAluno->id,
            'componenteCurricular' => 1,
            'quantidade' => 11,
            'etapa' => 2,
        ]),
    ];

    // Configura mock para Avaliacao_Model_FaltaComponenteDataMapper
    $mock = $this->getCleanMock('Avaliacao_Model_FaltaComponenteDataMapper');

    $mock->expects($this->at(0))
        ->method('findAll')
        ->with([], ['faltaAluno' => $faltaAluno->id], ['etapa' => 'ASC'])
        ->willReturn($faltasPersistidas);

    $mock->expects($this->at(1))
        ->method('save')
        ->with($faltas[0])
        ->willReturn(true);

    $mock->expects($this->at(2))
        ->method('save')
        ->with($faltas[1])
        ->willReturn(true);

    $this->_setFaltaAbstractDataMapperMock($mock);

    $service = $this->_getServiceInstance();
    $service->addFaltas($faltas);
    $service->saveFaltas();
});

test('salvas faltas de um componente e atualizada etapa da ultima instancia adicionada no boletim com etapas lancadas', function () {
    $this->markTestSkipped();

    $faltaAluno = $this->_getConfigOption('faltaAluno', 'instance');

    $faltas = [
        new Avaliacao_Model_FaltaComponente([
            'componenteCurricular' => 1,
            'quantidade' => 7,
            'etapa' => 2,
        ]),
        // Etapa omitida, será atribuída a etapa '3'
        new Avaliacao_Model_FaltaComponente([
            'componenteCurricular' => 1,
            'quantidade' => 9,
        ]),
    ];

    $faltasPersistidas = [
        new Avaliacao_Model_FaltaComponente([
            'componenteCurricular' => 1,
            'id' => 1,
            'faltaAluno' => $faltaAluno->id,
            'quantidade' => 8,
            'etapa' => 1,
        ]),
        new Avaliacao_Model_FaltaComponente([
            'componenteCurricular' => 1,
            'id' => 2,
            'faltaAluno' => $faltaAluno->id,
            'quantidade' => 11,
            'etapa' => 2,
        ]),
    ];

    // Configura mock para Avaliacao_Model_FaltaComponenteDataMapper
    $mock = $this->getCleanMock('Avaliacao_Model_FaltaComponenteDataMapper');

    $mock->expects($this->at(0))
        ->method('findAll')
        ->with([], ['faltaAluno' => $faltaAluno->id], ['etapa' => 'ASC'])
        ->willReturn($faltasPersistidas);

    $mock->expects($this->at(1))
        ->method('save')
        ->with($faltas[0])
        ->willReturn(true);

    $mock->expects($this->at(2))
        ->method('save')
        ->with($faltas[1])
        ->willReturn(true);

    $this->_setFaltaAbstractDataMapperMock($mock);

    $service = $this->_getServiceInstance();
    $service->addFaltas($faltas);
    $service->saveFaltas();

    $faltas = $service->getFaltas();

    $falta = array_shift($faltas);
    $this->assertEquals(2, $falta->etapa);

    // Etapa atribuída automaticamente
    $falta = array_shift($faltas);
    $this->assertEquals(3, $falta->etapa);
});
