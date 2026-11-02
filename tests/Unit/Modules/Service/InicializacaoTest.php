<?php

uses(Avaliacao_Service_TestCommon::class);

test('instancia lanca excecao caso codigo de matricula nao seja informado', function () {
    $this->expectException(CoreExt_Service_Exception::class);
    new Avaliacao_Service_Boletim;
});

test('instancia lanca excecao com opcao nao aceita pela classe', function () {
    $this->expectException(InvalidArgumentException::class);
    new Avaliacao_Service_Boletim(['matricula' => 1, 'foo' => 'bar']);
});

test('dados de matricula inicializados', function () {
    // Método _hydrateComponentes em IedFinder foi alterado. Terá que ser escrito um novo teste
    $this->markTestSkipped();
    $service = $this->_getServiceInstance();
    $options = $service->getOptions();

    $this->assertEquals(
        $this->_getConfigOption('usuario', 'cod_usuario'),
        $options['usuario']
    );

    $this->assertEquals(
        $this->_getConfigOption('matricula', 'aprovado'),
        $options['aprovado']
    );

    $this->assertEquals(
        $this->_getConfigOption('curso', 'hora_falta'),
        $options['cursoHoraFalta']
    );

    $this->assertEquals(
        $this->_getConfigOption('curso', 'carga_horaria'),
        $options['cursoCargaHoraria']
    );

    $this->assertEquals(
        $this->_getConfigOption('serie', 'carga_horaria'),
        $options['serieCargaHoraria']
    );

    $this->assertEquals(
        count($this->_getConfigOptions('anoLetivoModulo')),
        $options['etapas']
    );

    $expected = $this->_getConfigOptions('componenteCurricular');
    $dispensas = $this->_getDispensaDisciplina();
    foreach ($dispensas as $dispensa) {
        unset($expected[$dispensa['ref_cod_disciplina']]);
    }
    $actual = $service->getComponentes();
    $this->assertEquals($expected, $actual);
});

test('instancia regra de avaliacao atraves de um numero de matricula', function () {
    $service = $this->_getServiceInstance();
    $this->assertInstanceOf('RegraAvaliacao_Model_Regra', $service->getRegra());

    // TabelaArredondamento_Model_Tabela é recuperada através da instância de
    // RegraAvaliacao_Model_Regra
    $this->assertInstanceOf('TabelaArredondamento_Model_Tabela', $service->getRegraAvaliacaoTabelaArredondamento());
});
