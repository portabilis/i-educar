<?php

uses(Avaliacao_Service_FaltaSituacaoCommon::class);

beforeEach(function () {
    $this->configuraRegra(['tipoPresenca' => RegraAvaliacao_Model_TipoPresenca::GERAL]);
});

test('situacao faltas em andamento', function () {
    $this->markTestSkipped();

    $faltaAluno = $this->_getConfigOption('faltaAluno', 'instance');
    $this->_setUpFaltaAbstractDataMapperMock($faltaAluno, []);

    $expected = $this->_getExpectedSituacaoFaltas();

    // Configura a expectativa
    $expected->situacao = App_Model_MatriculaSituacao::EM_ANDAMENTO;
    $expected->porcentagemPresenca = 100;
    $expected->diasLetivos = 960;

    $service = $this->_getServiceInstance();
    $actual = $service->getSituacaoFaltas();

    $this->assertEquals($expected, $actual);
});

test('situacao faltas aprovado', function () {
    $this->markTestSkipped();

    $faltaAluno = $this->_getConfigOption('faltaAluno', 'instance');

    $faltas = [
        new Avaliacao_Model_FaltaGeral([
            'id' => 2,
            'quantidade' => 5,
            'etapa' => 2,
        ]),
        new Avaliacao_Model_FaltaGeral([
            'id' => 3,
            'quantidade' => 5,
            'etapa' => 3,
        ]),
        new Avaliacao_Model_FaltaGeral([
            'id' => 4,
            'quantidade' => 5,
            'etapa' => 4,
        ]),
    ];

    $this->_setUpFaltaAbstractDataMapperMock($faltaAluno, $faltas);

    $expected = $this->_getExpectedSituacaoFaltas();

    // Configura a expectativa
    $expected->situacao = App_Model_MatriculaSituacao::APROVADO;

    $expected->totalFaltas = array_sum(CoreExt_Entity::entityFilterAttr($faltas, 'id', 'quantidade'));
    $expected->horasFaltas = $expected->totalFaltas * $this->_getConfigOption('curso', 'hora_falta');
    $expected->porcentagemFalta = ($expected->horasFaltas / $this->_getConfigOption('serie', 'carga_horaria') * 100);
    $expected->porcentagemPresenca = 100 - $expected->porcentagemFalta;
    $expected->diasLetivos = 960;

    $service = $this->_getServiceInstance();
    $actual = $service->getSituacaoFaltas();

    $this->assertEquals($expected, $actual);
});

test('situacao faltas reprovado', function () {
    $this->markTestSkipped();

    $faltaAluno = $this->_getConfigOption('faltaAluno', 'instance');

    $faltas = [
        new Avaliacao_Model_FaltaGeral([
            'id' => 1,
            'quantidade' => 180,
            'etapa' => 1,
        ]),
        new Avaliacao_Model_FaltaGeral([
            'id' => 2,
            'quantidade' => 180,
            'etapa' => 2,
        ]),
        new Avaliacao_Model_FaltaGeral([
            'id' => 3,
            'quantidade' => 180,
            'etapa' => 3,
        ]),
        new Avaliacao_Model_FaltaGeral([
            'id' => 4,
            'quantidade' => 180,
            'etapa' => 4,
        ]),
    ];

    $this->_setUpFaltaAbstractDataMapperMock($faltaAluno, $faltas);

    $expected = $this->_getExpectedSituacaoFaltas();

    // Configura a expectativa
    $expected->situacao = App_Model_MatriculaSituacao::REPROVADO;

    $expected->totalFaltas = array_sum(CoreExt_Entity::entityFilterAttr($faltas, 'id', 'quantidade'));
    $expected->horasFaltas = $expected->totalFaltas * $this->_getConfigOption('curso', 'hora_falta');
    $expected->porcentagemFalta = ($expected->horasFaltas / $this->_getConfigOption('serie', 'carga_horaria') * 100);
    $expected->porcentagemPresenca = 100 - $expected->porcentagemFalta;
    $expected->diasLetivos = 960;

    $service = $this->_getServiceInstance();

    $this->assertEquals($expected, $service->getSituacaoFaltas());
});
