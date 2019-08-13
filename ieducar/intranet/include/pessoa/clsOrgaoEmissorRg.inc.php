<?php

require_once 'include/clsBanco.inc.php';
require_once 'include/Geral.inc.php';

class clsOrgaoEmissorRg
{
    public $idorg_rg;
    public $sigla;
    public $descricao;
    public $situacao;
    public $tabela;
    public $schema = 'cadastro';

    public function __construct($int_idorg_rg = false, $str_sigla = false, $str_descricao = false, $str_situacao = false)
    {
        $this->idorg_rg = $int_idorg_rg;
        $this->sigla = $str_sigla;
        $this->descricao = $str_descricao;
        $this->situacao = $str_situacao;
        $this->tabela = 'orgao_emissor_rg';
    }

    /**
     * Funcao que cadastra um novo registro com os valores atuais
     *
     * @return bool
     */
    public function cadastra()
    {
        $db = new clsBanco();

        if (is_string($this->sigla) && is_string($this->descricao) && is_string($this->situacao)) {
            $campos = '';
            $values = '';

            $db->Consulta("INSERT INTO {$this->schema}.{$this->tabela} ( sigla, descricao, situacao$campos ) VALUES ( '{$this->sigla}', '{$this->descricao}', '{$this->situacao}'$values )");

            return $db->InsertId("{$this->tabela}_idorg_rg_seq");

            return true;
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
        if (is_string($this->sigla) && is_string($this->descricao) && is_string($this->situacao)) {
            $set = "SET sigla = '{$this->sigla}', descricao = '{$this->descricao}', idnum = '{$this->situacao}' ";

            $db = new clsBanco();
            $db->Consulta("UPDATE {$this->schema}.{$this->tabela} $set WHERE idorg_rg = '$this->idorg_rg'");

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
        if (is_numeric($this->idorg_rg)) {
            $objDocumento = new clsDocumento(false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, $this->idorg_rg);
            if (!count($objDocumento->lista(false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, $this->idorg_rg))) {
                $db = new clsBanco();
                return true;
            }
        }
    }

    /**
     * Exibe uma lista baseada nos parametros de filtragem passados
     *
     * @return Array
     */
    public function lista($str_sigla = false, $str_descricao = false, $str_situacao = false, $int_limite_ini = 0, $int_limite_qtd = false, $str_orderBy = false)
    {
        $whereAnd = 'WHERE ';
        if (is_string($str_sigla)) {
            $where .= "{$whereAnd}sigla LIKE '%$str_sigla%'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_descricao)) {
            $where .= "{$whereAnd}descricao LIKE '%$str_descricao%'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_situacao)) {
            $where .= "{$whereAnd}situacao LIKE '%$str_situacao%'";
            $whereAnd = ' AND ';
        }

        if ($str_orderBy) {
            $orderBy = "ORDER BY $str_orderBy";
        }

        $limit = '';
        if (is_numeric($int_limite_ini) && is_numeric($int_limite_qtd)) {
            $limit = " LIMIT $int_limite_ini,$int_limite_qtd";
        }

        $db = new clsBanco();
        $db->Consulta("SELECT COUNT(0) AS total FROM {$this->schema}.{$this->tabela} $where");
        $db->ProximoRegistro();
        $total = $db->Campo('total');
        $db->Consulta("SELECT idorg_rg, sigla, descricao, situacao FROM {$this->schema}.{$this->tabela} $where $orderBy $limit");
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
        if ($this->idorg_rg) {
            $db = new clsBanco();
            $db->Consulta("SELECT idorg_rg, sigla, descricao, situacao FROM {$this->schema}.{$this->tabela} WHERE idorg_rg='{$this->idorg_rg}'");
            if ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();
                $this->idorg_rg = $tupla['idorg_rg'];
                $this->sigla = $tupla['sigla'];
                $this->descricao = $tupla['descricao'];
                $this->situacao = $tupla['situacao'];

                return $tupla;
            }
        }

        return false;
    }
}
