<?php

class Avaliacao_Service_FaltaComponenteSituacaoTest extends Avaliacao_Service_FaltaSituacaoCommon
{
    protected function setUp(): void
    {
        $this->_setRegraOption('tipoPresenca', RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE);
        parent::setUp();
    }

    public function testSituacaoFaltasEmAndamento()
    {
        $faltaAluno = $this->_getConfigOption('faltaAluno', 'instance');
        $this->_setUpFaltaAbstractDataMapperMock($faltaAluno, []);

        $expected = $this->_getExpectedSituacaoFaltas();

        // Configura a expectativa
        $expected->situacao = App_Model_MatriculaSituacao::EM_ANDAMENTO;
        $expected->porcentagemPresenca = 100;
        $expected->horasFaltas = 0.0;
        $expected->totalFaltas = 0;
        $expected->porcentagemFalta = 0.0;
        $expected->diasLetivos = 960;

        $service = $this->_getServiceInstance();
        $actual = $service->getSituacaoFaltas();

        $this->assertEquals($expected, $actual);
    }

    public function testSituacaoFaltasEmAndamentoUmComponenteAprovadoDeQuatroTotais()
    {
        $this->markTestSkipped();
        $faltaAluno = $this->_getConfigOption('faltaAluno', 'instance');
        $componentes = $this->_getConfigOptions('escolaSerieDisciplina');

        $faltas = [
            new Avaliacao_Model_FaltaComponente([
                'id' => 1,
                'componenteCurricular' => 2,
                'quantidade' => 5,
                'etapa' => 1
            ]),
            new Avaliacao_Model_FaltaComponente([
                'id' => 2,
                'componenteCurricular' => 2,
                'quantidade' => 5,
                'etapa' => 2
            ]),
            new Avaliacao_Model_FaltaComponente([
                'id' => 3,
                'componenteCurricular' => 2,
                'quantidade' => 5,
                'etapa' => 3
            ]),
            new Avaliacao_Model_FaltaComponente([
                'id' => 4,
                'componenteCurricular' => 2,
                'quantidade' => 5,
                'etapa' => 4
            ]),
        ];

        $this->_setUpFaltaAbstractDataMapperMock($faltaAluno, $faltas);

        $expected = $this->_getExpectedSituacaoFaltas();

        // Configura a expectativa
        $expected->situacao = App_Model_MatriculaSituacao::EM_ANDAMENTO;

        $expected->totalFaltas = array_sum(CoreExt_Entity::entityFilterAttr($faltas, 'id', 'quantidade'));
        $expected->horasFaltas = $expected->totalFaltas * $this->_getConfigOption('curso', 'hora_falta');
        $expected->porcentagemFalta = ($expected->horasFaltas / $this->_getConfigOption('serie', 'carga_horaria') * 100);
        $expected->porcentagemPresenca = 100 - $expected->porcentagemFalta;
        $expected->diasLetivos = 960;

        // Configura expectativa para o componente de id '1'
        $componenteHoraFalta =
            array_sum(CoreExt_Entity::entityFilterAttr($faltas, 'id', 'quantidade')) *
            $this->_getConfigOption('curso', 'hora_falta');

        $componentePorcentagemFalta =
            ($componenteHoraFalta / $componentes[0]['carga_horaria']) * 100;

        $componentePorcentagemPresenca = 100 - $componentePorcentagemFalta;

        $expected->componentesCurriculares[2] = new stdClass();
        $expected->componentesCurriculares[2]->situacao = App_Model_MatriculaSituacao::APROVADO;
        $expected->componentesCurriculares[2]->horasFaltas = $componenteHoraFalta;
        $expected->componentesCurriculares[2]->porcentagemFalta = $componentePorcentagemFalta;
        $expected->componentesCurriculares[2]->porcentagemPresenca = $componentePorcentagemPresenca;
        $expected->componentesCurriculares[2]->total = 20;

        $service = $this->_getServiceInstance();
        $actual = $service->getSituacaoFaltas();

        $this->assertEquals($expected, $actual);
    }

    public function testSituacaoFaltasAprovado()
    {
        //Método _hydrateComponentes em IedFinder foi alterado. Terá que ser escrito um novo teste
        $this->markTestSkipped();
        $faltaAluno = $this->_getConfigOption('faltaAluno', 'instance');
        $componentes = $this->_getConfigOptions('escolaSerieDisciplina');

        $faltas = [
            // Português
            new Avaliacao_Model_FaltaComponente([
                'id' => 5,
                'componenteCurricular' => 2,
                'quantidade' => 5,
                'etapa' => 1
            ]),
            new Avaliacao_Model_FaltaComponente([
                'id' => 6,
                'componenteCurricular' => 2,
                'quantidade' => 5,
                'etapa' => 2
            ]),
            new Avaliacao_Model_FaltaComponente([
                'id' => 7,
                'componenteCurricular' => 2,
                'quantidade' => 5,
                'etapa' => 3
            ]),
            new Avaliacao_Model_FaltaComponente([
                'id' => 8,
                'componenteCurricular' => 2,
                'quantidade' => 5,
                'etapa' => 4
            ]),
            // Ciências
            new Avaliacao_Model_FaltaComponente([
                'id' => 9,
                'componenteCurricular' => 3,
                'quantidade' => 5,
                'etapa' => 1
            ]),
            new Avaliacao_Model_FaltaComponente([
                'id' => 10,
                'componenteCurricular' => 3,
                'quantidade' => 5,
                'etapa' => 2
            ]),
            new Avaliacao_Model_FaltaComponente([
                'id' => 11,
                'componenteCurricular' => 3,
                'quantidade' => 5,
                'etapa' => 3
            ]),
            new Avaliacao_Model_FaltaComponente([
                'id' => 12,
                'componenteCurricular' => 3,
                'quantidade' => 5,
                'etapa' => 4
            ]),
            // Fisica
            new Avaliacao_Model_FaltaComponente([
                'id' => 13,
                'componenteCurricular' => 4,
                'quantidade' => 5,
                'etapa' => 1
            ]),
            new Avaliacao_Model_FaltaComponente([
                'id' => 14,
                'componenteCurricular' => 4,
                'quantidade' => 5,
                'etapa' => 2
            ]),
            new Avaliacao_Model_FaltaComponente([
                'id' => 15,
                'componenteCurricular' => 4,
                'quantidade' => 5,
                'etapa' => 3
            ]),
            new Avaliacao_Model_FaltaComponente([
                'id' => 16,
                'componenteCurricular' => 4,
                'quantidade' => 5,
                'etapa' => 4
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

        // Configura expectativa para o componente de id '2'
        $componenteHoraFalta =
            array_sum(CoreExt_Entity::entityFilterAttr(array_slice($faltas, 0, 4), 'id', 'quantidade')) *
            $this->_getConfigOption('curso', 'hora_falta');

        $componentePorcentagemFalta =
            ($componenteHoraFalta / $componentes[1]['carga_horaria']) * 100;

        $componentePorcentagemPresenca = 100 - $componentePorcentagemFalta;

        $expected->componentesCurriculares[2] = new stdClass();
        $expected->componentesCurriculares[2]->situacao = App_Model_MatriculaSituacao::APROVADO;
        $expected->componentesCurriculares[2]->horasFaltas = $componenteHoraFalta;
        $expected->componentesCurriculares[2]->porcentagemFalta = $componentePorcentagemFalta;
        $expected->componentesCurriculares[2]->porcentagemPresenca = $componentePorcentagemPresenca;
        $expected->componentesCurriculares[2]->total = 20;

        // Configura expectativa para o componente de id '3'
        $componenteHoraFalta =
            array_sum(CoreExt_Entity::entityFilterAttr(array_slice($faltas, 4, 4), 'id', 'quantidade')) *
            $this->_getConfigOption('curso', 'hora_falta');

        $componentePorcentagemFalta =
            ($componenteHoraFalta / $componentes[2]['carga_horaria']) * 100;

        $componentePorcentagemPresenca = 100 - $componentePorcentagemFalta;

        $expected->componentesCurriculares[3] = new stdClass();
        $expected->componentesCurriculares[3]->situacao = App_Model_MatriculaSituacao::APROVADO;
        $expected->componentesCurriculares[3]->horasFaltas = $componenteHoraFalta;
        $expected->componentesCurriculares[3]->porcentagemFalta = $componentePorcentagemFalta;
        $expected->componentesCurriculares[3]->porcentagemPresenca = $componentePorcentagemPresenca;
        $expected->componentesCurriculares[3]->total = 20;

        // Configura expectativa para o componente de id '4'
        $componenteHoraFalta =
            array_sum(CoreExt_Entity::entityFilterAttr(array_slice($faltas, 8, 4), 'id', 'quantidade')) *
            $this->_getConfigOption('curso', 'hora_falta');

        $componentePorcentagemFalta =
            ($componenteHoraFalta / $componentes[3]['carga_horaria']) * 100;

        $componentePorcentagemPresenca = 100 - $componentePorcentagemFalta;

        $expected->componentesCurriculares[4] = new stdClass();
        $expected->componentesCurriculares[4]->situacao = App_Model_MatriculaSituacao::APROVADO;
        $expected->componentesCurriculares[4]->horasFaltas = $componenteHoraFalta;
        $expected->componentesCurriculares[4]->porcentagemFalta = $componentePorcentagemFalta;
        $expected->componentesCurriculares[4]->porcentagemPresenca = $componentePorcentagemPresenca;
        $expected->componentesCurriculares[4]->total = 20.0;

        $service = $this->_getServiceInstance();

        $actual = $service->getSituacaoFaltas();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Faltas para componentes funcionam usam os mesmos critérios das faltas
     * gerais para a definição de aprovado ou reprovado: presença geral.
     */
    public function testSituacaoFaltasReprovado()
    {
        //Método _hydrateComponentes em IedFinder foi alterado. Terá que ser escrito um novo teste
        $this->markTestSkipped();
        $faltaAluno = $this->_getConfigOption('faltaAluno', 'instance');
        $componentes = $this->_getConfigOptions('escolaSerieDisciplina');

        $faltas = [
            // Português
            new Avaliacao_Model_FaltaComponente([
                'id' => 5,
                'componenteCurricular' => 2,
                'quantidade' => 40,
                'etapa' => 1
            ]),
            new Avaliacao_Model_FaltaComponente([
                'id' => 6,
                'componenteCurricular' => 2,
                'quantidade' => 40,
                'etapa' => 2
            ]),
            new Avaliacao_Model_FaltaComponente([
                'id' => 7,
                'componenteCurricular' => 2,
                'quantidade' => 35,
                'etapa' => 3
            ]),
            new Avaliacao_Model_FaltaComponente([
                'id' => 8,
                'componenteCurricular' => 2,
                'quantidade' => 15,
                'etapa' => 4
            ]),
            // Ciências
            new Avaliacao_Model_FaltaComponente([
                'id' => 9,
                'componenteCurricular' => 3,
                'quantidade' => 5,
                'etapa' => 1
            ]),
            new Avaliacao_Model_FaltaComponente([
                'id' => 10,
                'componenteCurricular' => 3,
                'quantidade' => 5,
                'etapa' => 2
            ]),
            new Avaliacao_Model_FaltaComponente([
                'id' => 11,
                'componenteCurricular' => 3,
                'quantidade' => 5,
                'etapa' => 3
            ]),
            new Avaliacao_Model_FaltaComponente([
                'id' => 12,
                'componenteCurricular' => 3,
                'quantidade' => 5,
                'etapa' => 4
            ]),
            // Fisica
            new Avaliacao_Model_FaltaComponente([
                'id' => 13,
                'componenteCurricular' => 4,
                'quantidade' => 30,
                'etapa' => 1
            ]),
            new Avaliacao_Model_FaltaComponente([
                'id' => 14,
                'componenteCurricular' => 4,
                'quantidade' => 40,
                'etapa' => 2
            ]),
            new Avaliacao_Model_FaltaComponente([
                'id' => 15,
                'componenteCurricular' => 4,
                'quantidade' => 20,
                'etapa' => 3
            ]),
            new Avaliacao_Model_FaltaComponente([
                'id' => 16,
                'componenteCurricular' => 4,
                'quantidade' => 20,
                'etapa' => 4
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

        // Configura expectativa para o componente de id '2'
        $componenteHoraFalta =
            array_sum(CoreExt_Entity::entityFilterAttr(array_slice($faltas, 0, 4), 'id', 'quantidade')) *
            $this->_getConfigOption('curso', 'hora_falta');

        $componentePorcentagemFalta =
            ($componenteHoraFalta / $componentes[1]['carga_horaria']) * 100;

        $componentePorcentagemPresenca = 100 - $componentePorcentagemFalta;

        $expected->componentesCurriculares[2] = new stdClass();
        $expected->componentesCurriculares[2]->situacao = App_Model_MatriculaSituacao::REPROVADO;
        $expected->componentesCurriculares[2]->horasFaltas = $componenteHoraFalta;
        $expected->componentesCurriculares[2]->porcentagemFalta = $componentePorcentagemFalta;
        $expected->componentesCurriculares[2]->porcentagemPresenca = $componentePorcentagemPresenca;
        $expected->componentesCurriculares[2]->total = 130.0;

        // Configura expectativa para o componente de id '3'
        $componenteHoraFalta =
            array_sum(CoreExt_Entity::entityFilterAttr(array_slice($faltas, 4, 4), 'id', 'quantidade')) *
            $this->_getConfigOption('curso', 'hora_falta');

        $componentePorcentagemFalta =
            ($componenteHoraFalta / $componentes[2]['carga_horaria']) * 100;

        $componentePorcentagemPresenca = 100 - $componentePorcentagemFalta;

        $expected->componentesCurriculares[3] = new stdClass();
        $expected->componentesCurriculares[3]->situacao = App_Model_MatriculaSituacao::APROVADO;
        $expected->componentesCurriculares[3]->horasFaltas = $componenteHoraFalta;
        $expected->componentesCurriculares[3]->porcentagemFalta = $componentePorcentagemFalta;
        $expected->componentesCurriculares[3]->porcentagemPresenca = $componentePorcentagemPresenca;
        $expected->componentesCurriculares[3]->total = 20.0;

        // Configura expectativa para o componente de id '4'
        $componenteHoraFalta =
            array_sum(CoreExt_Entity::entityFilterAttr(array_slice($faltas, 8, 4), 'id', 'quantidade')) *
            $this->_getConfigOption('curso', 'hora_falta');

        $componentePorcentagemFalta =
            ($componenteHoraFalta / $componentes[3]['carga_horaria']) * 100;

        $componentePorcentagemPresenca = 100 - $componentePorcentagemFalta;

        $expected->componentesCurriculares[4] = new stdClass();
        $expected->componentesCurriculares[4]->situacao = App_Model_MatriculaSituacao::REPROVADO;
        $expected->componentesCurriculares[4]->horasFaltas = $componenteHoraFalta;
        $expected->componentesCurriculares[4]->porcentagemFalta = $componentePorcentagemFalta;
        $expected->componentesCurriculares[4]->porcentagemPresenca = $componentePorcentagemPresenca;
        $expected->componentesCurriculares[4]->total = 110.0;

        $service = $this->_getServiceInstance();
        $actual = $service->getSituacaoFaltas();

        $this->assertEquals($expected, $actual);
    }
}
