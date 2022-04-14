<?php

class Avaliacao_Service_FaltaSituacaoCommon extends Avaliacao_Service_TestCommon
{
    protected function _setUpFaltaAbstractDataMapperMock(
        Avaliacao_Model_FaltaAluno $faltaAluno,
        array $faltas
    ) {
        // Configura mock para notas
        $mock = $this->getCleanMock('Avaliacao_Model_FaltaAbstractDataMapper');

        $mock->expects($this->any())
            ->method('findAll')
            ->with([], ['faltaAluno' => $faltaAluno->id], ['etapa' => 'ASC'])
            ->will($this->returnValue($faltas));

        $this->_setFaltaAbstractDataMapperMock($mock);
    }

    protected function _getExpectedSituacaoFaltas()
    {
        $faltaAluno = $this->_getConfigOption('faltaAluno', 'instance');

        // Valores retornados pelas instâncias de classes legadas
        $cursoHoraFalta = $this->_getConfigOption('curso', 'hora_falta');
        $serieCargaHoraria = $this->_getConfigOption('serie', 'carga_horaria');

        // Porcentagem configurada na regra
        $porcentagemPresenca = $this->_getRegraOption('porcentagemPresenca');

        $expected = new stdClass();
        $expected->situacao = 0;
        $expected->tipoFalta = 0;
        $expected->cargaHoraria = 0;
        $expected->cursoHoraFalta = 0;
        $expected->totalFaltas = 0;
        $expected->horasFaltas = 0;
        $expected->porcentagemFalta = 0;
        $expected->porcentagemPresenca = 100;
        $expected->porcentagemPresencaRegra = 0;
        $expected->componentesCurriculares = [];

        $expected->tipoFalta = $faltaAluno->get('tipoFalta');
        $expected->cursoHoraFalta = $cursoHoraFalta;
        $expected->porcentagemPresencaRegra = $porcentagemPresenca;
        $expected->cargaHoraria = $serieCargaHoraria;
        $expected->diasLetivos = null;

        return $expected;
    }
}
