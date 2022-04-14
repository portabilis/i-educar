<?php

class clsEstadoCivil
{
    public $ideciv;
    public $descricao;
    public $tabela;
    public $schema;

    /**
     * Construtor
     *
     * @return Object:clsEstadoCivil
     */
    public function __construct($ideciv = false, $descricao = false)
    {
        $this->ideciv = $ideciv;
        $this->descricao = $descricao;

        $this->tabela = 'estado_civil';
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

        if (is_numeric($this->ideciv) && is_string($this->descricao)) {
            $db->Consulta("INSERT INTO {$this->$this->schema}.{$this->tabela} (ideciv, descricao) VALUES ($this->ideciv, $this->descricao)");
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
        if (is_numeric($this->ideciv) && is_string($this->descricao)) {
            $db = new clsBanco();
            $db->Consulta("UPDATE {$this->$this->schema}.{$this->tabela} SET descricao = '$this->descricao' WHERE ideciv = '$this->ideciv' ");

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
        if (is_numeric($this->ideciv) && is_string($this->descricao)) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->$this->schema}.{$this->tabela} WHERE ideciv = {$this->ideciv}");

            return true;
        }

        return false;
    }

    /**
     * Exibe uma lista baseada nos parametros de filtragem passados
     *
     * @return Array
     */
    public function lista($int_ideciv = false, $str_descricao = false, $str_ordenacao = 'descricao', $int_limite_ini = 0, $int_limite_qtd = 20)
    {
        $where = '';
        $whereAnd = 'WHERE ';

        if (is_numeric($int_ideciv)) {
            $where .= "{$whereAnd}ideciv = '$int_ideciv'";
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
        $db->Consulta("SELECT ideciv, descricao FROM {$this->schema}.{$this->tabela} $where $orderBy $limit");
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
        if ($this->ideciv) {
            $db = new clsBanco();
            $db->Consulta("SELECT ideciv, descricao FROM {$this->schema}.{$this->tabela} WHERE ideciv = {$this->ideciv}");
            if ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();

                return $tupla;
            }
        }

        return false;
    }
}
