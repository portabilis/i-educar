<?php

trait Avaliacao_Service_Boletim_Regra
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
    public function getRegra()
    {
        return $this->_regra;
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
        return $this->getRegra()->get('tipoPresenca');
    }

    /**
     * Retorna o tipo de parecer descritivo da regra de avaliação.
     *
     * @return int
     */
    public function getRegraTipoParecerDescritivo()
    {
        return $this->getRegra()->get('parecerDescritivo');
    }

    /**
     * @return TabelaArredondamento_Model_Tabela
     */
    public function getRegraTabelaArredondamento()
    {
        return $this->getRegra()->tabelaArredondamento;
    }
}
