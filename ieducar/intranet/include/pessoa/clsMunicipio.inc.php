<?php

require_once 'include/clsBanco.inc.php';
require_once 'include/Geral.inc.php';

class clsMunicipio
{
    public $idmun;
    public $nome;
    public $sigla_uf;
    public $area_km2;
    public $idmreg;
    public $idasmun;
    public $cod_ibge;
    public $geom;
    public $tipo;
    public $idmun_pai;
    public $idpes_cad;
    public $idpes_rev;
    public $origem_gravacao;
    public $operacao;
    public $tabela;
    public $schema = 'public';
    public $_total;

    public function __construct($int_idmun = false, $str_nome = false, $str_sigla_uf = false, $int_area_km2 = false, $int_idmreg = false, $int_idasmun = false, $int_cod_ibge = false, $str_geom = false, $str_tipo = false, $int_idmun_pai = false, $int_idpes_cad = false, $int_idpes_rev = false, $str_origem_gravacao = false, $str_operacao = false)
    {
        if ($int_idmun) {
            $this->idmun = $int_idmun;
        }

        $this->nome = $str_nome;

        $objUf = new clsUf($str_sigla_uf);
        if ($objUf->detalhe()) {
            $this->sigla_uf = $str_sigla_uf;
        }

        $this->area_km2 = $int_area_km2;
        $this->idmreg = $int_idmreg;

        $objPais = new clsPais($int_idasmun);
        if ($objPais->detalhe()) {
            $this->idasmun = $int_idasmun;
        }

        $this->cod_ibge = $int_cod_ibge;
        $this->geom = $str_geom;
        $this->tipo = $str_tipo;
        $this->idpes_cad = $int_idpes_cad;
        $this->idpes_rev = $int_idpes_rev;
        $this->operacao = $str_operacao;
        $this->origem_gravacao = $str_origem_gravacao;

        $objPais = new clsPais($int_idmun_pai);
        if ($objPais->detalhe()) {
            $this->idmun_pai = $int_idmun_pai;
        }

        $this->tabela = 'municipio';
    }

    /**
     * Funcao que cadastra um novo registro com os valores atuais
     *
     * @return bool
     */
    public function cadastra()
    {
        $db = new clsBanco();

        if (is_numeric($this->idmun) && is_string($this->nome) && is_string($this->sigla_uf) && is_string($this->tipo) && is_numeric($this->idpes_cad) && is_string($this->operacao) && is_string($this->origem_gravacao)) {
            $campos = '';
            $values = '';

            if (is_numeric($this->area_km2)) {
                $campos .= ', area_km2';
                $values .= ", '{$this->area_km2}'";
            }
            if (is_numeric($this->idpes_cad)) {
                $campos .= ', idpes_cad';
                $values .= ", '{$this->idpes_cad}'";
            }
            if (is_string($this->operacao)) {
                $campos .= ', operacao';
                $values .= ", '{$this->operacao}'";
            }
            if (is_string($this->origem_gravacao)) {
                $campos .= ', origem_gravacao';
                $values .= ", '{$this->origem_gravacao}'";
            }
            if (is_numeric($this->idmreg)) {
                $campos .= ', idmreg';
                $values .= ", '{$this->idmreg}'";
            }
            if (is_numeric($this->idasmun)) {
                $campos .= ', idasmun';
                $values .= ", '{$this->idasmun}'";
            }
            if (is_numeric($this->cod_ibge)) {
                $campos .= ', cod_ibge';
                $values .= ", '{$this->cod_ibge}'";
            }
            if (is_string($this->geom)) {
                $campos .= ', geom';
                $values .= ", '{$this->geom}'";
            }
            if (is_numeric($this->idmun_pai)) {
                $campos .= ', idmun_pai';
                $values .= ", '{$this->idmun_pai}'";
            }

            $db->Consulta("INSERT INTO {$this->schema}.{$this->tabela} ( idmun, nome, sigla_uf, tipo, data_cad$campos ) VALUES ( '{$this->idmun}', '{$this->nome}', '{$this->sigla_uf}', '{$this->tipo}', NOW()$values )");

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
        if (is_string($this->nome) && is_string($this->sigla_uf) && is_string($this->tipo)) {
            $set = "SET nome = '{$this->nome}', sigla_uf = '{$this->sigla_uf}', tipo = '{$this->tipo}'";

            if (is_numeric($this->area_km2)) {
                $set .= ", area_km2 = '{$this->area_km2}'";
            } else {
                $set .= ', area_km2 = NULL';
            }

            if (is_numeric($this->idmreg)) {
                $set .= ", idmreg = '{$this->idmreg}'";
            } else {
                $set .= ', idmreg = NULL';
            }

            if (is_numeric($this->idasmun)) {
                $set .= ", idasmun = '{$this->idasmun}'";
            } else {
                $set .= ', idasmun = NULL';
            }

            if (is_numeric($this->cod_ibge)) {
                $set .= ", cod_ibge = '{$this->cod_ibge}'";
            } else {
                $set .= ', cod_ibge = NULL';
            }

            if (is_string($this->geom)) {
                $set .= ", geom = '{$this->geom}'";
            } else {
                $set .= ', geom = NULL';
            }

            if (is_numeric($this->idmun_pai)) {
                $set .= ", idmun_pai = '{$this->idmun_pai}'";
            } else {
                $set .= ', idmun_pai = NULL';
            }

            $db = new clsBanco();
            $db->Consulta("UPDATE {$this->schema}.{$this->tabela} $set WHERE idmun = '$this->idmun'");

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
        if (is_numeric($this->idmun)) {
            $objBairro = new clsBairro();
            $listaBairro = $objBairro->lista($this->idmun);

            $objLog = new clsLogradouro();
            $listaLog = $objLog->lista(false, false, $this->idmun);

            if (!count($listaBairro) && !count($listaLog)) {
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
    public function lista($str_nome = false, $str_sigla_uf = false, $int_area_km2 = false, $int_idmreg = false, $int_idasmun = false, $int_cod_ibge = false, $str_geom = false, $str_tipo = false, $int_idmun_pai = false, $int_limite_ini = false, $int_limite_qtd = false, $str_orderBy = false)
    {
        $whereAnd = 'WHERE ';
        if (is_string($str_nome)) {
            $where .= "{$whereAnd} translate(upper(nome),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') LIKE translate(upper('%{$str_nome}%'),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN')";
            $whereAnd = ' AND ';
        }
        if (is_string($str_sigla_uf)) {
            $where .= "{$whereAnd}sigla_uf LIKE '%$str_sigla_uf%'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_area_km2)) {
            $where .= "{$whereAnd}area_km2 = '$int_area_km2'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_idmreg)) {
            $where .= "{$whereAnd}idmreg = '$int_area_km2'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_idasmun)) {
            $where .= "{$whereAnd}idasmun = '$int_idasmun'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_cod_ibge)) {
            $where .= "{$whereAnd}cod_ibge = '$int_cod_ibge'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_geom)) {
            $where .= "{$whereAnd}geom LIKE '%$str_geom%'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_tipo)) {
            $where .= "{$whereAnd}tipo LIKE '%$str_geom%'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_idmun_pai)) {
            $where .= "{$whereAnd}idmun_pai = '$int_idmun_pai'";
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
        $db->Consulta("SELECT idmun, nome, sigla_uf, area_km2, idmreg, idasmun, cod_ibge, geom , tipo, idmun_pai FROM {$this->schema}.{$this->tabela} $where $orderBy $limit");
        $resultado = [];
        while ($db->ProximoRegistro()) {
            $tupla = $db->Tupla();
            $tupla['sigla_uf'] = new clsUf($tupla['sigla_uf']);
            $tupla['idasmun'] = new clsUf($tupla['idasmun']);
            $tupla['idmun_pai'] = new clsUf($tupla['idamun_pai']);
            $tupla['total'] = $total;
            $this->_total = $total;
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
        if ($this->idmun) {
            $db = new clsBanco();
            $db->Consulta("SELECT idmun, nome, sigla_uf, area_km2, idmreg, idasmun, cod_ibge, geom , tipo, idmun_pai FROM {$this->schema}.{$this->tabela} WHERE idmun={$this->idmun}");
            if ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();
                $this->idmun = $tupla['idmun'];
                $this->nome = $tupla['nome'];
                $this->sigla_uf = $tupla['sigla_uf'];
                $this->area_km2 = $tupla['area_km2'];
                $this->idmreg = $tupla['idmreg'];
                $this->idasmun = $tupla['idasmun'];
                $this->cod_ibge = $tupla['cod_ibge'];
                $this->geom = $tupla['geom'];
                $this->tipo = $tupla['tipo'];
                $this->idmun_pai = $tupla['idmun_pai'];

                $tupla['sigla_uf'] = new clsUf($tupla['sigla_uf']);
                $tupla['idasmun'] = new clsUf($tupla['idasmun']);
                $tupla['idmun_pai'] = new clsUf($tupla['idamun_pai'] ?? null);

                return $tupla;
            }
        }

        return false;
    }
}
