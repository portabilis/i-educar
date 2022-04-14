<?php

trait Avaliacao_Service_Boletim_FaltaAluno
{
    /**
     * Uma instância de Avaliacao_Model_FaltaAluno, que é a entrada que contém
     * o cruzamento de matrícula com as faltas do aluno nos diversos
     * componentes cursados ou no geral.
     *
     * @var Avaliacao_Model_FaltaAluno
     */
    protected $_faltaAluno;

    /**
     * @return Avaliacao_Model_FaltaAluno|null
     */
    protected function _getFaltaAluno()
    {
        if (!is_null($this->_faltaAluno)) {
            return $this->_faltaAluno;
        }

        $faltaAluno = $this->getFaltaAlunoDataMapper()->findAll(
            [],
            ['matricula' => $this->getOption('matricula')]
        );

        if (0 == count($faltaAluno)) {
            return null;
        }

        $this->_setFaltaAluno($faltaAluno[0]);

        return $this->_faltaAluno;
    }

    /**
     * @param Avaliacao_Model_FaltaAluno $falta
     *
     * @return $this
     */
    protected function _setFaltaAluno(Avaliacao_Model_FaltaAluno $falta)
    {
        $this->_faltaAluno = $falta;
        $tipoFaltaAtual = $this->_faltaAluno->get('tipoFalta');
        $tipoFaltaRegraAvaliacao = $this->getRegraAvaliacaoTipoPresenca();

        if ($tipoFaltaAtual != $tipoFaltaRegraAvaliacao) {
            $this->_faltaAluno->tipoFalta = $tipoFaltaRegraAvaliacao;
            $this->getFaltaAlunoDataMapper()->save($this->_faltaAluno);
        }

        return $this;
    }
}
