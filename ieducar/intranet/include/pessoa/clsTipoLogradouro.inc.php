<?php

require_once 'include/clsBanco.inc.php';
require_once 'include/Geral.inc.php';

class clsTipoLogradouro
{
    public $idtlog;
    public $descricao;
    public $tabela;
    public $schema;

    public function __construct($idtlog = false, $descricao = false)
    {
        $this->idtlog = $idtlog;
        $this->descricao = $descricao;

        $this->tabela = 'tipo_logradouro';
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

        if (is_numeric($this->idtlog) && is_string($this->descricao)) {
            $db->Consulta("INSERT INTO {$this->schema}.{$this->tabela} (idtlog,  descricao) VALUES ( '{$this->idtlog}', '{$this->descricao}')");
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
        if (is_numeric($this->idtlog) && is_string($this->descricao)) {
            $db = new clsBanco();
            $db->Consulta("UPDATE {$this->schema}.{$this->tabela} SET descricao = '$this->descricao' WHERE idtlog = '$this->idtlog' ");

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
        if (is_numeric($this->idtlog) && is_string($this->descricao)) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->schema}.{$this->tabela} WHERE idtlog = {$this->idtlog}");

            return true;
        }

        return false;
    }

    /**
     * Exibe uma lista baseada nos parametros de filtragem passados
     *
     * @return Array
     */
    public function lista($int_idtlog = false, $str_descricao = false, $str_ordenacao = 'descricao', $int_limite_ini = false, $int_limite_qtd = false)
    {
        $whereAnd = 'WHERE ';
        if (is_numeric($int_idtlog)) {
            $where .= "{$whereAnd}idtlog = '$int_idtlog'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_descricao)) {
            $where .= "{$whereAnd}descricao ILIKE '%$str_descricao%'";
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
        $db->Consulta("SELECT idtlog, descricao FROM {$this->schema}.{$this->tabela} $where $orderBy $limit");

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
        $db = new clsBanco();
        $db->Consulta("SELECT idtlog, descricao FROM {$this->schema}.{$this->tabela} WHERE idtlog = '{$this->idtlog}' ");
        if ($db->ProximoRegistro()) {
            $tupla = $db->Tupla();

            return $tupla;
        }

        return false;
    }
}
