<?php

require_once 'include/clsBanco.inc.php';
require_once 'include/Geral.inc.php';

class clsCepLogradouroBairro
{
    public $idlog;
    public $cep;
    public $idbai;
    public $tabela;
    public $schema;

    public function __construct($idlog = false, $cep = false, $idbai = false)
    {
        $objLogradouro = new clsLogradouro($idlog);

        if ($objLogradouro->detalhe()) {
            $this->idlog = $idlog;
        }

        $objCepLogradouro = new clsCepLogradouro($cep, $idlog);
        if ($objCepLogradouro->detalhe()) {
            $this->cep = $cep;
        }

        $objBairro = new clsBairro($idbai);
        if ($objBairro->detalhe()) {
            $this->idbai = $idbai;
        }

        $this->tabela = 'cep_logradouro_bairro';
        $this->schema = 'urbano';
    }

    /**
     * Funcao que cadastra um novo registro com os valores atuais
     *
     * @return bool
     */
    public function cadastra()
    {
        $db = new clsBanco();

        if (is_numeric($this->idlog) && is_numeric($this->cep) && is_numeric($this->idbai)) {
            $db->Consulta("INSERT INTO {$this->schema}.{$this->tabela} (cep,idlog,idbai, origem_gravacao, data_cad, operacao) VALUES ('{$this->cep}', '{$this->idlog}', '{$this->idbai}', 'U', NOW(), 'I')");
        }

        return false;
    }

    /**
     * Edita o registro atual
     *
     * @return bool
     */
    public function edita()
    {
        if (is_numeric($this->cep) && is_numeric($this->idlog) && is_numeric($this->idbai)) {
            return true;
        }

        return false;
    }

    /**
     * Remove o registro atual
     *
     * @return bool
     */
    public function exclui()
    {
        if (is_numeric($this->cep) && is_numeric($this->idlog) && is_numeric($this->idbai)) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->schema}.{$this->tabela} WHERE cep = {$this->cep} AND idlog = {$this->idlog} AND idbai = {$this->idbai}");

            return true;
        }

        return false;
    }

    /**
     * Exibe uma lista baseada nos parametros de filtragem passados
     *
     * @return Array
     */
    public function lista($int_idlog = false, $int_cep = false, $int_idbai = false, $str_ordenacao = 'idlog', $int_limite_ini = 0, $int_limite_qtd = 20)
    {
        $whereAnd = 'WHERE ';
        if (is_numeric($int_idlog)) {
            $where .= "{$whereAnd}idlog = '$int_idlog'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_cep)) {
            $where .= "{$whereAnd}cep::varchar LIKE '%$int_cep%'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_idbai)) {
            $where .= "{$whereAnd}idbai =  '$int_idbai'";
        }

        $orderBy = '';
        if (is_string($str_ordenacao)) {
            $orderBy = "ORDER BY $str_ordenacao";
        }
        $limit = '';
        if (is_numeric($int_limite_ini) && is_numeric($int_limite_qtd)) {
            $limit = " LIMIT $int_limite_ini,$int_limite_qtd";
        }

        $db = new clsBanco();
        $db->Consulta("SELECT COUNT(0) AS total FROM {$this->schema}.{$this->tabela} $where");
        $db->ProximoRegistro();
        $total = $db->Campo('total');
        $db->Consulta("SELECT idlog, cep, idbai FROM {$this->schema}.{$this->tabela} $where $orderBy $limit");
        $resultado = [];
        while ($db->ProximoRegistro()) {
            $tupla = $db->Tupla();
            $idlog = $tupla['idlog'];
            $tupla['idlog'] = new clsCepLogradouro($tupla['cep'], $tupla['idlog']);
            $tupla['cep'] = new clsCepLogradouro($tupla['cep'], $idlog);
            $tupla['idbai'] = new clsBairro($tupla['idbai']);
            $tupla['total'] = $total;
            $resultado[] = $tupla;
        }
        if (count($resultado)) {
            return $resultado;
        }

        return false;
    }

    /**
     * Retorna um array com os detalhes do objeto
     *
     * @return Array
     */
    public function detalhe()
    {
        if ($this->cep && $this->idbai && $this->idlog) {
            $db = new clsBanco();
            $db->Consulta("SELECT idlog, cep, idbai FROM {$this->schema}.{$this->tabela} WHERE idlog = {$this->idlog} AND cep = {$this->cep} AND idbai = {$this->idbai}");
            if ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();

                $idlog = $tupla['idlog'];
                $tupla['idlog'] = new clsCepLogradouro($tupla['cep'], $tupla['idlog']);
                $tupla['cep'] = new clsCepLogradouro($tupla['cep'], $idlog);
                $tupla['idbai'] = new clsBairro($tupla['idbai']);

                return $tupla;
            }
        }

        return false;
    }

    /**
     * Retorna um array com os dados de um registro.
     *
     * @return array
     */
    public function existe()
    {
        if (is_numeric($this->cep) && is_numeric($this->idlog) && is_numeric($this->idbai)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->schema}.{$this->tabela} WHERE cep = '{$this->cep}' AND idlog = '{$this->idlog}' AND idbai = '{$this->idbai}' ");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }
}
