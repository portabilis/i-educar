<?php

require_once 'include/clsBanco.inc.php';
require_once 'include/Geral.inc.php';

class clsUf
{
    public $sigla_uf;
    public $nome;
    public $geom;
    public $idpais;
    public $tabela;
    public $schema = 'public';

    public function __construct($str_sigla_uf = false, $str_nome = false, $str_geom = false, $int_idpais = false)
    {
        $this->sigla_uf = $str_sigla_uf;
        $this->nome = $str_nome;
        $this->geom = $str_geom;

        $objPais = new clsPais($int_idpais);
        if ($objPais->detalhe()) {
            $this->idpais = $int_idpais;
        }

        $this->tabela = 'uf';
    }

    /**
     * Funcao que cadastra um novo registro com os valores atuais
     *
     * @return bool
     */
    public function cadastra()
    {
        $db = new clsBanco();

        if (is_string($this->sigla_uf) && is_string($this->nome)) {
            $campos = '';
            $values = '';

            if (is_string($this->geom)) {
                $campos .= ', geom';
                $values .= ", '{$this->geom}'";
            }
            if (is_numeric($this->idpais)) {
                $campos .= ', idpais';
                $values .= ", '{$this->idpais}'";
            }

            $db->Consulta("INSERT INTO {$this->schema}.{$this->tabela} ( sigla_uf, nome$campos ) VALUES ( '{$this->sigla_uf}', '{$this->nome}'");

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
        if (is_string($this->nome)) {
            $set = "SET nome = '{$this->nome}'";

            if (is_string($this->geom)) {
                $set .= ", geom = '{$this->geom}'";
            } else {
                $set .= ', geom = NULL';
            }

            if (is_numeric($this->idpais)) {
                $set .= ", idpais = '{$this->idpais}'";
            } else {
                $set .= ', idpais = NULL';
            }

            $db = new clsBanco();
            $db->Consulta("UPDATE {$this->schema}.{$this->tabela} $set WHERE idlog = '$this->idlog'");

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
        if (is_string($this->sigla_uf)) {
            $objCidade = new clsMunicipio();
            $listaCidade = $objCidade->lista(false, $this->sigla_uf);

            if (!count($listaCidade)) {
                $db = new clsBanco();
                return true;
            }

            return false;
        }

        return false;
    }

    /**
     * Retorna um array com os registros da tabela public.uf
     *
     * @return array
     */
    public function lista(
        $str_nome = false,
        $str_geom = false,
        $int_idpais = false,
        $int_limite_ini = false,
        $int_limite_qtd = false,
        $str_orderBy = 'sigla_uf ASC'
    ) {
        $whereAnd = 'WHERE ';

        if (is_string($str_nome)) {
            $where .= "{$whereAnd}nome LIKE '%$str_nome%'";
            $whereAnd = ' AND ';
        }

        if (is_string($str_geom)) {
            $where .= "{$whereAnd}geom LIKE '%$str_geom%'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_idpais)) {
            $where .= "{$whereAnd}idpais = '$int_idpais'";
            $whereAnd = ' AND ';
        } else {
            $idpais = config('legacy.app.locale.country');
            $where .= "{$whereAnd}idpais = '$idpais'";
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

        $db->Consulta("SELECT sigla_uf, nome, geom, idpais FROM {$this->schema}.{$this->tabela} $where $orderBy $limit");
        $resultado = [];

        while ($db->ProximoRegistro()) {
            $tupla = $db->Tupla();
            $tupla['idpais'] = new clsPais($tupla['idpais']);
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
        if ($this->sigla_uf) {
            $db = new clsBanco();
            $db->Consulta("SELECT sigla_uf, nome, geom, idpais FROM {$this->schema}.{$this->tabela} WHERE sigla_uf='{$this->sigla_uf}'");
            if ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();
                $this->sigla_uf = $tupla['sigla_uf'];
                $this->nome = $tupla['nome'];
                $this->geom = $tupla['geom'];
                $this->idpais = $tupla['idpais'];
                $tupla['int_idpais'] = $tupla['idpais'];
                $tupla['idpais'] = new clsPais($tupla['idpais']);

                return $tupla;
            }
        }

        return false;
    }
}
