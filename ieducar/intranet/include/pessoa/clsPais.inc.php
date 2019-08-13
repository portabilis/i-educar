<?php

require_once 'include/clsBanco.inc.php';
require_once 'include/Geral.inc.php';

class clsPais
{
    public $idpais;
    public $nome;
    public $geom;
    public $tabela;
    public $schema = 'public';

    public function __construct($int_idpais = false, $int_idpais__ = false, $str_nome = false, $str_geom = false)
    {
        if ($int_idpais) {
            $this->idpais = $int_idpais;
        } else {
            $this->idpais = $int_idpais__;
        }
        $this->nome = $str_nome;
        $this->geom = $str_geom;

        $this->tabela = 'pais';
    }

    /**
     * Funcao que cadastra um novo registro com os valores atuais
     *
     * @return bool
     */
    public function cadastra()
    {
        $db = new clsBanco();

        if (is_numeric($this->idpais) && is_string($this->nome)) {
            $campos = '';
            $values = '';

            if (is_string($this->geom)) {
                $campos .= ', geom';
                $values .= ", '{$this->geom}'";
            }

            $db->Consulta("INSERT INTO {$this->schema}.{$this->tabela} ( idpais, nome$campos ) VALUES ( '{$this->idpais}', '{$this->nome}'");

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

            $db = new clsBanco();
            $db->Consulta("UPDATE {$this->schema}.{$this->tabela} $set WHERE idpais = '$this->ispais'");

            return true;
        }

        return false;
    }

    /**
     * Remove o registro atual
     *
     * @return bool
     */
    public function exclui($int_cod_pessoa)
    {
        if (is_numeric($this->idpais)) {
            $objUf = new clsUf();
            $listaUf = $objUf->lista(false, false, false, $this->idpais);

            if (!count($listaUf)) {
                $db = new clsBanco();
                return true;
            }

            return false;
        }

        return false;
    }

    /**
     * Exibe uma lista baseada nos parametros de filtragem passados
     *
     * @return Array
     */
    public function lista($int_idpais = false, $str_nome = false, $str_geom = false, $int_limite_ini = 0, $int_limite_qtd = 20, $str_orderBy = false)
    {
        $whereAnd = 'WHERE ';
        if (is_string($str_nome)) {
            $where .= "{$whereAnd} translate(upper(nome),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') LIKE translate(upper('%{$nome}%'),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN')";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_idpais)) {
            $where .= "{$whereAnd}idpais = '$idpais'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_geom)) {
            $where .= "{$whereAnd}geom LIKE '%$geom%'";
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
        $db->Consulta("SELECT idpais, nome, geom FROM {$this->schema}.{$this->tabela} $where $orderBy $limit");
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
        if ($this->idpais) {
            $db = new clsBanco();
            $db->Consulta("SELECT idpais, nome, geom FROM {$this->schema}.{$this->tabela} WHERE idpais='{$this->idpais}'");
            if ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();
                $this->idpais = $tupla['idpais'];
                $this->nome = $tupla['nome'];
                $this->geom = $tupla['geom'];

                return $tupla;
            }
        }

        return false;
    }
}
