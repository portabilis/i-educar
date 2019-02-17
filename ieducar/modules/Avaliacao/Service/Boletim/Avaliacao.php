<?php

trait Avaliacao_Service_Boletim_Avaliacao
{
    /**
     * Notas do aluno nos componentes cursados.
     *
     * @var array
     */
    protected $_notasComponentes = [];

    /**
     * Notas do aluno por etapa.
     *
     * @var array
     */
    protected $_notasGerais = [];

    /**
     * Médias do aluno nos componentes cursados.
     *
     * @var array
     */
    protected $_mediasComponentes = [];

    /**
     * Média geral do aluno.
     *
     * @var array
     */
    protected $_mediasGerais = [];

    /**
     * Notas adicionadas no boletim para inclusão ou edição.
     *
     * @var array
     */
    protected $_notas = [];

    /**
     * Faltas do aluno nos componentes cursados.
     *
     * @var array
     */
    protected $_faltasComponentes = [];

    /**
     * Faltas do aluno no geral.
     *
     * @var array
     */
    protected $_faltasGerais = [];

    /**
     * Faltas adicionadas no boletim para inclusão ou edição.
     *
     * @var array
     */
    protected $_faltas = [];

    /**
     * Pareceres descritivos adicionados no boletim para inclusão ou edição.
     *
     * @var array
     */
    protected $_pareceres = [];

    /**
     * Pareceres descritivos do aluno nos componentes cursados.
     *
     * @var array
     */
    protected $_pareceresComponentes = [];

    /**
     * Pareceres descritivos do aluno no geral.
     *
     * @var array
     */
    protected $_pareceresGerais = [];

    /**
     * Retorna as instâncias de Avaliacao_Model_NotaComponente do aluno.
     *
     * @return array
     */
    public function getNotasComponentes()
    {
        return $this->_notasComponentes;
    }

    /**
     * @param array $notasComponentes
     *
     * @return $this
     */
    public function setNotasComponentes(array $notasComponentes)
    {
        $this->_notasComponentes = $notasComponentes;

        return $this;
    }

    /**
     * @return array
     */
    public function getNotasGerais()
    {
        return $this->_notasGerais;
    }

    /**
     * @param array $notasGerais
     *
     * @return $this
     */
    public function setNotasGerais(array $notasGerais)
    {
        $this->_notasGerais = $notasGerais;

        return $this;
    }

    /**
     * Retorna as instâncias de Avaliacao_Model_NotaComponenteMedia do aluno.
     *
     * @return array
     */
    public function getMediasComponentes()
    {
        return $this->_mediasComponentes;
    }

    /**
     * Retorna as instâncias de Avaliacao_Model_NotaComponenteMedia do aluno.
     *
     * @param array $mediasComponentes
     *
     * @return $this
     */
    public function setMediasComponentes(array $mediasComponentes)
    {
        $this->_mediasComponentes = $mediasComponentes;

        return $this;
    }

    /**
     * Retorna as instâncias de Avaliacao_Model_MediaGeral do aluno.
     *
     * @return array
     */
    public function getMediasGerais()
    {
        return $this->_mediasGerais;
    }

    /**
     * Retorna as instâncias de Avaliacao_Model_MediaGeral do aluno.
     *
     * @param array $mediasGerais
     * 
     * @return $this
     */
    public function setMediasGerais(array $mediasGerais)
    {
        $this->_mediasGerais = $mediasGerais;

        return $this;
    }

    /**
     * @return array
     */
    public function getNotas()
    {
        return $this->_notas;
    }

    /**
     * Retorna as instâncias de Avaliacao_Model_FaltaComponente do aluno.
     *
     * @return array
     */
    public function getFaltasComponentes()
    {
        return $this->_faltasComponentes;
    }

    /**
     * Retorna as instâncias de Avaliacao_Model_FaltaGeral do aluno.
     *
     * @return array
     */
    public function getFaltasGerais()
    {
        return $this->_faltasGerais;
    }

    /**
     * @return array
     */
    public function getFaltas()
    {
        return $this->_faltas;
    }

    /**
     * Getter para as instâncias de Avaliacao_Model_ParecerDescritivoAbstract
     * adicionadas no boletim (não persistidas).
     *
     * @return array
     */
    public function getPareceres()
    {
        return $this->_pareceres;
    }

    /**
     * Retorna as instâncias de Avaliacao_Model_ParecerDescritivoComponente do
     * aluno.
     *
     * @return array
     */
    public function getPareceresComponentes()
    {
        return $this->_pareceresComponentes;
    }

    /**
     * Retorna as instâncias de Avaliacao_Model_ParecerDescritivoGeral do
     * aluno.
     *
     * @return array
     */
    public function getPareceresGerais()
    {
        return $this->_pareceresGerais;
    }
}
