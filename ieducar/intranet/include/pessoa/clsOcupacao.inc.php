<?php

require_once 'include/clsBanco.inc.php';
require_once 'include/Geral.inc.php';

class clsOcupacao
{
    public $idocup;
    public $descricao;
    public $tabela;
    public $schema;

    public function __construct($idocup = false, $descricao = false)
    {
        $this->idocup = $idocup;
        $this->descricao = $descricao;

        $this->tabela = 'ocupacao';
        $this->schema = 'cadastro';
    }

    /**
     * Funcao que cadastra um novo registro com os valores atuais
     *
     * @return bool
     */
    public function cadastra()
    {
        $db = new clsBanco();

        if (is_numeric($this->idocup) && is_string($this->descricao)) {
            $db->Consulta("INSERT INTO {$this->schema}.{$this->tabela} (idocup, descricao) VALUES ($this->idocup, $this->descricao)");
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
        if (is_numeric($this->idocup) && is_string($this->descricao)) {
            $db = new clsBanco();
            $db->Consulta("UPDATE {$this->schema}.{$this->tabela} SET descricao = '$this->descricao' WHERE idocup = '$this->idocup' ");

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
        if (is_numeric($this->idocup) && is_string($this->descricao)) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->schema}.{$this->tabela} WHERE idocup = {$this->idocup}");

            return true;
        }

        return false;
    }

    /**
     * Exibe uma lista baseada nos parametros de filtragem passados
     *
     * @return Array
     */
    public function lista($int_idocup = false, $str_descricao = false, $str_ordenacao = 'descricao', $int_limite_ini = 0, $int_limite_qtd = 20)
    {
        // verificacoes de filtros a serem usados
        $whereAnd = 'WHERE ';
        if (is_numeric($int_idocup)) {
            $where .= "{$whereAnd}idocup = '$int_idocup'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_descricao)) {
            $where .= "{$whereAnd}descricao ILIKE  '%$str_descricao%'";
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
        $db->Consulta("SELECT idocup, descricao FROM {$this->schema}.{$this->tabela} $where $orderBy $limit");
        $resultado = [];
        while ($db->ProximoRegistro()) {
            $tupla = $db->Tupla();
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
        if ($this->idocup) {
            $db = new clsBanco();
            $db->Consulta("SELECT idocup, descricao FROM {$this->schema}.{$this->tabela} WHERE idocup = {$this->idocup}");
            if ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();

                return $tupla;
            }
        }

        return false;
    }
}
