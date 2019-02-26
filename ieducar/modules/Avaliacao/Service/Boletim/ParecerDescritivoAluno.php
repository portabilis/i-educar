<?php

trait Avaliacao_Service_Boletim_ParecerDescritivoAluno
{
    /**
     * Uma instância de Avaliacao_Model_ParecerDescritivoAluno, que é a entrada
     * que contém o cruzamento de matrícula com os pareceres do aluno nos
     * diversos componentes cursados ou no geral.
     *
     * @var Avaliacao_Model_ParecerDescritivoAluno
     */
    protected $_parecerDescritivoAluno;

    /**
     * @return Avaliacao_Model_ParecerDescritivoAluno|null
     */
    protected function _getParecerDescritivoAluno()
    {
        if (!is_null($this->_parecerDescritivoAluno)) {
            return $this->_parecerDescritivoAluno;
        }

        $parecerDescritivoAluno = $this->getParecerDescritivoAlunoDataMapper()->findAll(
            [],
            ['matricula' => $this->getOption('matricula')]
        );

        if (0 == count($parecerDescritivoAluno)) {
            return null;
        }

        $this->_setParecerDescritivoAluno($parecerDescritivoAluno[0]);

        return $this->_parecerDescritivoAluno;
    }

    /**
     * @param Avaliacao_Model_ParecerDescritivoAluno $parecerDescritivoAluno
     *
     * @return $this
     */
    protected function _setParecerDescritivoAluno(Avaliacao_Model_ParecerDescritivoAluno $parecerDescritivoAluno)
    {
        $this->_parecerDescritivoAluno = $parecerDescritivoAluno;

        return $this;
    }
}
