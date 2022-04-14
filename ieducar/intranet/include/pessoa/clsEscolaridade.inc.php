<?php

class clsEscolaridade
{
    public $idesco;
    public $descricao;
    public $tabela;
    public $schema;

    public function __construct($idesco = false, $descricao = false)
    {
        $this->idesco = $idesco;
        $this->descricao = $descricao;

        $this->tabela = 'escolaridade';
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

        if (is_string($this->descricao)) {
            $this->idesco = $db->UnicoCampo('select max(idesco) from cadastro.escolaridade');
            $db->Consulta("INSERT INTO {$this->schema}.{$this->tabela} (idesco, descricao) VALUES ($this->idesco, $this->descricao)");

            return $this->idesco;
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
        if (is_numeric($this->idesco) && is_string($this->descricao)) {
            $db = new clsBanco();
            $db->Consulta("UPDATE {$this->schema}.{$this->tabela} SET descricao = '$this->descricao' WHERE idesco = '$this->idesco' ");

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
        if (is_numeric($this->idesco) && is_string($this->descricao)) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->schema}.{$this->tabela} WHERE idesco = {$this->idesco}");

            return true;
        }

        return false;
    }

    /**
     * Exibe uma lista baseada nos parametros de filtragem passados
     *
     * @return Array
     */
    public function lista($int_idesco = false, $str_descricao = false, $str_ordenacao = 'descricao', $int_limite_ini = 0, $int_limite_qtd = 30)
    {
        $whereAnd = 'WHERE ';

        if (is_numeric($int_idesco)) {
            $where .= "{$whereAnd}idesco = '$int_idesco'";
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
        $db->Consulta("SELECT idesco, descricao FROM {$this->schema}.{$this->tabela} $where $orderBy $limit");
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
        if ($this->idesco) {
            $db = new clsBanco();
            $db->Consulta("SELECT idesco, descricao FROM {$this->schema}.{$this->tabela} WHERE idesco = {$this->idesco}");
            if ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();

                return $tupla;
            }
        }

        return false;
    }
}
