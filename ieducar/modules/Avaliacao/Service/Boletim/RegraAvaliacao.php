<?php

trait Avaliacao_Service_Boletim_RegraAvaliacao
{
    /**
     * Instância da regra de avaliação, com o qual o serviço irá utilizar para
     * decidir o fluxo da lógica.
     *
     * @var RegraAvaliacao_Model_Regra
     */
    protected $_regra;

    /**
     * @return RegraAvaliacao_Model_Regra
     */
    public function getRegraAvaliacao()
    {
        return $this->_regra;
    }

    /**
     * @deprecated
     *
     * @see Avaliacao_Service_Boletim_RegraAvaliacao::getRegraAvaliacao()
     *
     * @return RegraAvaliacao_Model_Regra
     */
    public function getRegra()
    {
        return $this->getRegraAvaliacao();
    }

    /**
     * @param RegraAvaliacao_Model_Regra $regra
     *
     * @return $this
     */
    protected function _setRegra(RegraAvaliacao_Model_Regra $regra)
    {
        $this->_regra = $regra;

        return $this;
    }

    /**
     * Retorna o tipo de presença da regra de avaliação.
     *
     * @return int
     */
    public function getRegraTipoPresenca()
    {
        return $this->getRegraAvaliacao()->get('tipoPresenca');
    }

    /**
     * Retorna o tipo de parecer descritivo da regra de avaliação.
     *
     * @return int
     */
    public function getRegraTipoParecerDescritivo()
    {
        return $this->getRegraAvaliacao()->get('parecerDescritivo');
    }

    /**
     * @return TabelaArredondamento_Model_Tabela
     */
    public function getRegraTabelaArredondamento()
    {
        return $this->getRegraAvaliacao()->tabelaArredondamento;
    }

    /**
     * Verifica se a regra de avaliacação possui recuperação final.
     *
     * @return bool
     */
    public function hasRegraFormulaRecuperacao()
    {
        return ! is_null($this->getRegraAvaliacao()->get('formulaRecuperacao'));
    }
}
