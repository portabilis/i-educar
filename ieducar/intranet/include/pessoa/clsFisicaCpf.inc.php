<?php

use Illuminate\Support\Facades\Session;

require_once('include/clsBanco.inc.php');
require_once('include/Geral.inc.php');

class clsFisicaCpf
{
    public $idpes;
    public $cpf;
    public $idpes_cad;
    public $idpes_rev;
    public $tabela;
    public $schema;

    public function __construct($idpes = false, $cpf = false, $idpes_cad = false, $idpes_rev = false)
    {
        $this->idpes = $idpes;
        $this->idpes_cad = $idpes_cad ? $idpes_cad : Session::get('id_pessoa');
        $this->idpes_rev = $idpes_rev ? $idpes_rev : Session::get('id_pessoa');
        $this->cpf = $cpf;

        $this->tabela = 'fisica';
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

        if (is_numeric($this->idpes) && is_numeric($this->cpf) && $this->idpes_cad) {
            $db->Consulta("UPDATE {$this->schema}.{$this->tabela} SET cpf = '$this->cpf'  WHERE idpes = '$this->idpes' ");

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
        if (is_numeric($this->idpes) && is_numeric($this->cpf) && is_numeric($this->idpes_rev)) {
            $db = new clsBanco();
            $db->Consulta("UPDATE {$this->schema}.{$this->tabela} SET cpf = '$this->cpf'  WHERE idpes = '$this->idpes' ");

            return true;
        }

        return false;
    }

    /**
     * Exibe uma lista baseada nos parametros de filtragem passados
     *
     * @return Array
     */
    public function lista($int_idpes = false, $int_cpf = false, $str_ordenacao = 'idpes', $int_limite_ini = false, $int_limite_qtd = false)
    {
        $whereAnd = 'WHERE ';

        if (is_numeric($int_idpes)) {
            $where .= "{$whereAnd}idpes = '$int_idpes'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_cpf)) {
            $where .= "{$whereAnd}cpf ILIKE '%$int_cpf%' OR cpf ILIKE '$int_cpf%' ";
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
        $db->Consulta("SELECT idpes, cpf FROM {$this->schema}.{$this->tabela} $where $orderBy $limit");
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
     * Exibe uma lista de idpes baseada nos parametros de filtragem passados
     *
     * @return Array
     */
    public function listaCod($int_idpes = false, $int_cpf = false, $str_ordenacao = 'idpes', $int_limite_ini = false, $int_limite_qtd = false)
    {
        $whereAnd = 'WHERE ';

        if (is_numeric($int_idpes)) {
            $where .= "{$whereAnd}idpes = '$int_idpes'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_cpf)) {
            $temp_cpf = $int_cpf + 0;
            $where .= "{$whereAnd}cpf ILIKE '%$int_cpf%' OR cpf ILIKE '$temp_cpf%' ";
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
        $db->Consulta("SELECT idpes FROM {$this->schema}.{$this->tabela} $where $orderBy $limit");
        $resultado = [];
        while ($db->ProximoRegistro()) {
            $tupla = $db->Tupla();
            $resultado[] = $tupla['idpes'];
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
        if ($this->cpf) {
            $db = new clsBanco();
            $db->Consulta("SELECT idpes, cpf FROM {$this->schema}.{$this->tabela} WHERE cpf = {$this->cpf} ");
            if ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();

                return $tupla;
            }
        } elseif ($this->idpes) {
            $db = new clsBanco();
            $db->Consulta("SELECT idpes, cpf FROM {$this->schema}.{$this->tabela} WHERE idpes = {$this->idpes} ");
            if ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();

                return $tupla;
            }
        }

        return false;
    }
}
