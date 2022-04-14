<?php

abstract class Avaliacao_Service_NotaSituacaoCommon extends Avaliacao_Service_TestCommon
{
    protected function _setUpNotaComponenteMediaDataMapperMock(
        Avaliacao_Model_NotaAluno $notaAluno,
        array $medias
    ) {
        // Configura mock para notas
        $mock = $this->getCleanMock('Avaliacao_Model_NotaComponenteMediaDataMapper');

        $mock->expects($this->any())
            ->method('findAll')
            ->with([], ['notaAluno' => $notaAluno->id])
            ->will($this->returnValue($medias));

        $this->_setNotaComponenteMediaDataMapperMock($mock);
    }

    /**
     * Um componente em exame, já que por padrão a regra de avaliação define uma
     * fórmula de recuperação. Quatro médias lançadas, 3 aprovadas.
     */
    public function testSituacaoComponentesCurricularesUmComponenteLancadoEmExameDeQuatroComponentesTotaisLancadosAprovados()
    {
        $this->markTestSkipped();
        // Expectativa
        $expected = new stdClass();
        $expected->situacao = App_Model_MatriculaSituacao::EM_EXAME;
        $expected->componentesCurriculares = [];

        // Matemática estará em exame
        $expected->componentesCurriculares[1] = new stdClass();
        $expected->componentesCurriculares[1]->situacao = App_Model_MatriculaSituacao::EM_EXAME;

        $expected->componentesCurriculares[2] = new stdClass();
        $expected->componentesCurriculares[2]->situacao = App_Model_MatriculaSituacao::APROVADO;

        $expected->componentesCurriculares[3] = new stdClass();
        $expected->componentesCurriculares[3]->situacao = App_Model_MatriculaSituacao::APROVADO;

        $expected->componentesCurriculares[4] = new stdClass();
        $expected->componentesCurriculares[4]->situacao = App_Model_MatriculaSituacao::APROVADO;

        $notaAluno = $this->_getConfigOption('notaAluno', 'instance');

        // Nenhuma média lançada
        $medias = [
            new Avaliacao_Model_NotaComponenteMedia([
                'notaAluno' => $notaAluno->id,
                'componenteCurricular' => 1,
                'media' => 5,
                'mediaArredondada' => 5,
                'etapa' => 4
            ]),
            new Avaliacao_Model_NotaComponenteMedia([
                'notaAluno' => $notaAluno->id,
                'componenteCurricular' => 2,
                'media' => 6,
                'mediaArredondada' => 6,
                'etapa' => 4
            ]),
            new Avaliacao_Model_NotaComponenteMedia([
                'notaAluno' => $notaAluno->id,
                'componenteCurricular' => 3,
                'media' => 6,
                'mediaArredondada' => 6,
                'etapa' => 4
            ]),
            new Avaliacao_Model_NotaComponenteMedia([
                'notaAluno' => $notaAluno->id,
                'componenteCurricular' => 4,
                'media' => 6,
                'mediaArredondada' => 6,
                'etapa' => 4
            ])
        ];

        // Configura mock para notas
        $this->_setUpNotaComponenteMediaDataMapperMock($notaAluno, $medias);

        $service = $this->_getServiceInstance();

        $this->assertEquals($expected, $service->getSituacaoComponentesCurriculares());
    }

    public function testSituacaoComponentesCurricularesUmComponenteLancadoEmExameDeQuatroComponentesTotaisLancadosDoisAprovadosUmAndamento()
    {
        $this->markTestSkipped();
        // Expectativa
        $expected = new stdClass();
        $expected->situacao = App_Model_MatriculaSituacao::EM_ANDAMENTO;
        $expected->componentesCurriculares = [];

        // Matemática estará em exame
        $expected->componentesCurriculares[1] = new stdClass();
        $expected->componentesCurriculares[1]->situacao = App_Model_MatriculaSituacao::EM_EXAME;

        $expected->componentesCurriculares[2] = new stdClass();
        $expected->componentesCurriculares[2]->situacao = App_Model_MatriculaSituacao::EM_ANDAMENTO;

        $expected->componentesCurriculares[3] = new stdClass();
        $expected->componentesCurriculares[3]->situacao = App_Model_MatriculaSituacao::APROVADO;

        $expected->componentesCurriculares[4] = new stdClass();
        $expected->componentesCurriculares[4]->situacao = App_Model_MatriculaSituacao::APROVADO;

        $notaAluno = $this->_getConfigOption('notaAluno', 'instance');

        // Nenhuma média lançada
        $medias = [
            new Avaliacao_Model_NotaComponenteMedia([
                'notaAluno' => $notaAluno->id,
                'componenteCurricular' => 1,
                'media' => 5,
                'mediaArredondada' => 5,
                'etapa' => 4
            ]),
            new Avaliacao_Model_NotaComponenteMedia([
                'notaAluno' => $notaAluno->id,
                'componenteCurricular' => 2,
                'media' => 5.75,
                'mediaArredondada' => 5,
                'etapa' => 3
            ]),
            new Avaliacao_Model_NotaComponenteMedia([
                'notaAluno' => $notaAluno->id,
                'componenteCurricular' => 3,
                'media' => 6,
                'mediaArredondada' => 6,
                'etapa' => 4
            ]),
            new Avaliacao_Model_NotaComponenteMedia([
                'notaAluno' => $notaAluno->id,
                'componenteCurricular' => 4,
                'media' => 6,
                'mediaArredondada' => 6,
                'etapa' => 4
            ])
        ];

        // Configura mock para notas
        $this->_setUpNotaComponenteMediaDataMapperMock($notaAluno, $medias);

        $service = $this->_getServiceInstance();

        $this->assertEquals($expected, $service->getSituacaoComponentesCurriculares());
    }

    public function testSituacaoComponentesCurricularesUmComponenteLancadoEmExameDeQuatroComponentesTotaisLancadosUmAprovadoAposExameEDoisAprovados()
    {
        $this->markTestSkipped();
        // Expectativa
        $expected = new stdClass();
        $expected->situacao = App_Model_MatriculaSituacao::EM_EXAME;
        $expected->componentesCurriculares = [];

        // Matemática estará em exame
        $expected->componentesCurriculares[1] = new stdClass();
        $expected->componentesCurriculares[1]->situacao = App_Model_MatriculaSituacao::EM_EXAME;

        $expected->componentesCurriculares[2] = new stdClass();
        $expected->componentesCurriculares[2]->situacao = App_Model_MatriculaSituacao::APROVADO_APOS_EXAME;

        $expected->componentesCurriculares[3] = new stdClass();
        $expected->componentesCurriculares[3]->situacao = App_Model_MatriculaSituacao::APROVADO;

        $expected->componentesCurriculares[4] = new stdClass();
        $expected->componentesCurriculares[4]->situacao = App_Model_MatriculaSituacao::APROVADO;

        $notaAluno = $this->_getConfigOption('notaAluno', 'instance');

        // Nenhuma média lançada
        $medias = [
            new Avaliacao_Model_NotaComponenteMedia([
                'notaAluno' => $notaAluno->id,
                'componenteCurricular' => 1,
                'media' => 5,
                'mediaArredondada' => 5,
                'etapa' => 4
            ]),
            new Avaliacao_Model_NotaComponenteMedia([
                'notaAluno' => $notaAluno->id,
                'componenteCurricular' => 2,
                'media' => 6.5,
                'mediaArredondada' => 6,
                'etapa' => 'Rc'
            ]),
            new Avaliacao_Model_NotaComponenteMedia([
                'notaAluno' => $notaAluno->id,
                'componenteCurricular' => 3,
                'media' => 6,
                'mediaArredondada' => 6,
                'etapa' => 4
            ]),
            new Avaliacao_Model_NotaComponenteMedia([
                'notaAluno' => $notaAluno->id,
                'componenteCurricular' => 4,
                'media' => 6,
                'mediaArredondada' => 6,
                'etapa' => 4
            ])
        ];

        // Configura mock para notas
        $this->_setUpNotaComponenteMediaDataMapperMock($notaAluno, $medias);

        $service = $this->_getServiceInstance();

        $this->assertEquals($expected, $service->getSituacaoComponentesCurriculares());
    }

    public function testSituacaoComponentesCurricularesUmComponenteLancadoEmExameDeQuatroComponentesTotaisLancadosUmAprovadoAposExameUmReprovadoEOutroAprovado()
    {
        $this->markTestSkipped();
        // Expectativa
        $expected = new stdClass();
        $expected->situacao = App_Model_MatriculaSituacao::EM_EXAME;
        $expected->componentesCurriculares = [];

        // Matemática estará em exame
        $expected->componentesCurriculares[1] = new stdClass();
        $expected->componentesCurriculares[1]->situacao = App_Model_MatriculaSituacao::EM_EXAME;

        $expected->componentesCurriculares[2] = new stdClass();
        $expected->componentesCurriculares[2]->situacao = App_Model_MatriculaSituacao::APROVADO_APOS_EXAME;

        $expected->componentesCurriculares[3] = new stdClass();
        $expected->componentesCurriculares[3]->situacao = App_Model_MatriculaSituacao::REPROVADO;

        $expected->componentesCurriculares[4] = new stdClass();
        $expected->componentesCurriculares[4]->situacao = App_Model_MatriculaSituacao::APROVADO;

        $notaAluno = $this->_getConfigOption('notaAluno', 'instance');

        // Nenhuma média lançada
        $medias = [
            new Avaliacao_Model_NotaComponenteMedia([
                'notaAluno' => $notaAluno->id,
                'componenteCurricular' => 1,
                'media' => 5,
                'mediaArredondada' => 5,
                'etapa' => 4
            ]),
            new Avaliacao_Model_NotaComponenteMedia([
                'notaAluno' => $notaAluno->id,
                'componenteCurricular' => 2,
                'media' => 6.5,
                'mediaArredondada' => 6,
                'etapa' => 'Rc'
            ]),
            new Avaliacao_Model_NotaComponenteMedia([
                'notaAluno' => $notaAluno->id,
                'componenteCurricular' => 3,
                'media' => 5,
                'mediaArredondada' => 5,
                'etapa' => 'Rc'
            ]),
            new Avaliacao_Model_NotaComponenteMedia([
                'notaAluno' => $notaAluno->id,
                'componenteCurricular' => 4,
                'media' => 6,
                'mediaArredondada' => 6,
                'etapa' => 4
            ])
        ];

        // Configura mock para notas
        $this->_setUpNotaComponenteMediaDataMapperMock($notaAluno, $medias);

        $service = $this->_getServiceInstance();

        $this->assertEquals($expected, $service->getSituacaoComponentesCurriculares());
    }

    /**
     * Um componente reprovado, com uma regra sem recuperação. Quatro médias
     * lançadas, 3 aprovadas.
     */
    public function testSituacaoComponentesCurricularesUmComponenteLancadoReprovadoUmComponenteAbaixoDaMedia()
    {
        $this->markTestSkipped();
        $this->_setRegraOption('formulaRecuperacao', null);

        // Expectativa
        $expected = new stdClass();
        $expected->situacao = App_Model_MatriculaSituacao::REPROVADO;
        $expected->componentesCurriculares = [];

        // Matemática estará em exame
        $expected->componentesCurriculares[1] = new stdClass();
        $expected->componentesCurriculares[1]->situacao = App_Model_MatriculaSituacao::REPROVADO;

        $expected->componentesCurriculares[2] = new stdClass();
        $expected->componentesCurriculares[2]->situacao = App_Model_MatriculaSituacao::APROVADO;

        $expected->componentesCurriculares[3] = new stdClass();
        $expected->componentesCurriculares[3]->situacao = App_Model_MatriculaSituacao::APROVADO;

        $expected->componentesCurriculares[4] = new stdClass();
        $expected->componentesCurriculares[4]->situacao = App_Model_MatriculaSituacao::APROVADO;

        $notaAluno = $this->_getConfigOption('notaAluno', 'instance');

        // Nenhuma média lançada
        $medias = [
            new Avaliacao_Model_NotaComponenteMedia([
                'notaAluno' => $notaAluno->id,
                'componenteCurricular' => 1,
                'media' => 5,
                'mediaArredondada' => 5,
                'etapa' => 4
            ]),
            new Avaliacao_Model_NotaComponenteMedia([
                'notaAluno' => $notaAluno->id,
                'componenteCurricular' => 2,
                'media' => 6,
                'mediaArredondada' => 6,
                'etapa' => 4
            ]),
            new Avaliacao_Model_NotaComponenteMedia([
                'notaAluno' => $notaAluno->id,
                'componenteCurricular' => 3,
                'media' => 6,
                'mediaArredondada' => 6,
                'etapa' => 4
            ]),
            new Avaliacao_Model_NotaComponenteMedia([
                'notaAluno' => $notaAluno->id,
                'componenteCurricular' => 4,
                'media' => 6,
                'mediaArredondada' => 6,
                'etapa' => 4
            ])
        ];

        // Configura mock para notas
        $this->_setUpNotaComponenteMediaDataMapperMock($notaAluno, $medias);

        $service = $this->_getServiceInstance();

        $this->assertEquals($expected, $service->getSituacaoComponentesCurriculares());
    }
}
