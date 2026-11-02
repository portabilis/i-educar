<?php

uses(Avaliacao_Service_TestCommon::class);

test('salvar notas de um componente curricular no boletim em recuperacao', function () {
    $this->markTestSkipped();
    $notaAluno = $this->_getConfigOption('notaAluno', 'instance');

    $notas = [
        new Avaliacao_Model_NotaComponente([
            'componenteCurricular' => 1,
            'nota' => 5,
            'etapa' => 1,
        ]),
        new Avaliacao_Model_NotaComponente([
            'componenteCurricular' => 1,
            'nota' => 5,
            'etapa' => 2,
        ]),
        new Avaliacao_Model_NotaComponente([
            'componenteCurricular' => 1,
            'nota' => 6,
            'etapa' => 3,
        ]),
        new Avaliacao_Model_NotaComponente([
            'componenteCurricular' => 1,
            'nota' => 6,
            'etapa' => 4,
        ]),
        new Avaliacao_Model_NotaComponente([
            'componenteCurricular' => 1,
            'nota' => 6,
            'etapa' => 'Rc',
        ]),
    ];

    $media = new Avaliacao_Model_NotaComponenteMedia([
        'notaAluno' => $notaAluno->id,
        'componenteCurricular' => 1,
        'media' => 5.7,
        'mediaArredondada' => 5,
        'etapa' => 'Rc',
    ]);

    $media->markOld();

    // Configura mock para Avaliacao_Model_NotaComponenteDataMapper
    $mock = $this->getCleanMock('Avaliacao_Model_NotaComponenteDataMapper');

    $mock->expects($this->at(0))
        ->method('findAll')
        ->with([], ['notaAluno' => $notaAluno->id], ['etapa' => 'ASC'])
        ->willReturn([]);

    $mock->expects($this->at(1))
        ->method('save')
        ->with($notas[0])
        ->willReturn(true);

    $mock->expects($this->at(2))
        ->method('save')
        ->with($notas[1])
        ->willReturn(true);

    $mock->expects($this->at(3))
        ->method('save')
        ->with($notas[2])
        ->willReturn(true);

    $mock->expects($this->at(4))
        ->method('save')
        ->with($notas[3])
        ->willReturn(true);

    $mock->expects($this->at(5))
        ->method('save')
        ->with($notas[4])
        ->willReturn(true);

    $mock->expects($this->at(6))
        ->method('findAll')
        ->with([], ['notaAluno' => $notaAluno->id], ['etapa' => 'ASC'])
        ->willReturn($notas);

    $this->_setNotaComponenteDataMapperMock($mock);

    // Configura mock para Avaliacao_Model_NotaComponenteMediaDataMapper
    $mock = $this->getCleanMock('Avaliacao_Model_NotaComponenteMediaDataMapper');

    $mock->expects($this->at(0))
        ->method('findAll')
        ->with([], ['notaAluno' => $notaAluno->id])
        ->willReturn([]);

    $mock->expects($this->at(1))
        ->method('find')
        ->with([$notaAluno->id, $this->_getConfigOption('matricula', 'cod_matricula')])
        ->willReturn([]);

    $mock->expects($this->at(2))
        ->method('save')
        ->with($media)
        ->willReturn(true);

    $this->_setNotaComponenteMediaDataMapperMock($mock);

    $service = $this->_getServiceInstance();
    $service->addNotas($notas);
    $service->saveNotas();

    $notasSalvas = $service->getNotas();

    $etapas = array_merge(
        range(1, count($this->_getConfigOptions('anoLetivoModulo'))),
        ['Rc']
    );

    foreach ($notasSalvas as $notaSalva) {
        $key = array_search($notaSalva->etapa, $etapas, false);
        $this->assertTrue($key !== false);
        unset($etapas[$key]);
    }
});

test('salvar notas de um componente curricular no boletim em recuperacao com notas lancadas', function () {
    $this->markTestSkipped();
    $notaAluno = $this->_getConfigOption('notaAluno', 'instance');

    $notas = [
        new Avaliacao_Model_NotaComponente([
            'componenteCurricular' => 1,
            'nota' => 5,
        ]),
    ];

    $notasPersistidas = [
        new Avaliacao_Model_NotaComponente([
            'id' => 1,
            'notaAluno' => $notaAluno->id,
            'componenteCurricular' => 1,
            'nota' => 6,
            'notaArredondada' => 6,
            'etapa' => 1,
        ]),
        new Avaliacao_Model_NotaComponente([
            'id' => 2,
            'notaAluno' => $notaAluno->id,
            'componenteCurricular' => 1,
            'nota' => 6,
            'notaArredondada' => 6,
            'etapa' => 2,
        ]),
        new Avaliacao_Model_NotaComponente([
            'id' => 3,
            'notaAluno' => $notaAluno->id,
            'componenteCurricular' => 1,
            'nota' => 6,
            'notaArredondada' => 6,
            'etapa' => 3,
        ]),
        new Avaliacao_Model_NotaComponente([
            'id' => 4,
            'notaAluno' => $notaAluno->id,
            'componenteCurricular' => 1,
            'nota' => 6,
            'notaArredondada' => 6,
            'etapa' => 4,
        ]),
    ];

    // Configura mock para Avaliacao_Model_NotaComponenteDataMapper
    $mock = $this->getCleanMock('Avaliacao_Model_NotaComponenteDataMapper');

    $mock->expects($this->at(0))
        ->method('findAll')
        ->with([], ['notaAluno' => $notaAluno->id], ['etapa' => 'ASC'])
        ->willReturn($notasPersistidas);

    $mock->expects($this->at(1))
        ->method('save')
        ->with($notas[0])
        ->willReturn(true);

    $notasSalvas = array_merge($notasPersistidas, $notas);

    $mock->expects($this->at(2))
        ->method('findAll')
        ->with([], ['notaAluno' => $notaAluno->id], ['etapa' => 'ASC'])
        ->willReturn($notasSalvas);

    $this->_setNotaComponenteDataMapperMock($mock);

    $service = $this->_getServiceInstance();
    $service->addNotas($notas);
    $service->saveNotas();

    $etapas = array_merge(
        range(1, count($this->_getConfigOptions('anoLetivoModulo'))),
        ['Rc']
    );

    foreach ($notasSalvas as $notaSalva) {
        $key = array_search($notaSalva->etapa, $etapas, false);
        $this->assertTrue($key !== false);
        unset($etapas[$key]);
    }
});
