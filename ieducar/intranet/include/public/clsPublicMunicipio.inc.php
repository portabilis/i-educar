<?php

use iEducar\Legacy\Model;

require_once 'include/public/geral.inc.php';

class clsPublicMunicipio extends Model
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
    public $idpes_rev;
    public $idpes_cad;
    public $data_rev;
    public $data_cad;
    public $origem_gravacao;
    public $operacao;

    /**
     * Construtor (PHP 4)
     *
     * @param integer idmun
     * @param string nome
     * @param string sigla_uf
     * @param integer area_km2
     * @param integer idmreg
     * @param integer idasmun
     * @param integer cod_ibge
     * @param string geom
     * @param string tipo
     * @param integer idmun_pai
     * @param integer idpes_rev
     * @param integer idpes_cad
     * @param string data_rev
     * @param string data_cad
     * @param string origem_gravacao
     * @param string operacao
     *
     * @return object
     */
    public function __construct($idmun = null, $nome = null, $sigla_uf = null, $area_km2 = null, $idmreg = null, $idasmun = null, $cod_ibge = null, $geom = null, $tipo = null, $idmun_pai = null, $idpes_rev = null, $idpes_cad = null, $data_rev = null, $data_cad = null, $origem_gravacao = null, $operacao = null)
    {
        $db = new clsBanco();
        $this->_schema = 'public.';
        $this->_tabela = "{$this->_schema}municipio";

        $this->_campos_lista = $this->_todos_campos = 'idmun, nome, sigla_uf, area_km2, idmreg, idasmun, cod_ibge, geom, tipo, idmun_pai, idpes_rev, idpes_cad, data_rev, data_cad, origem_gravacao, operacao';

        if (is_string($sigla_uf)) {
                    $this->sigla_uf = $sigla_uf;
        }
        if (is_numeric($idpes_rev)) {
                    $this->idpes_rev = $idpes_rev;
        }
        if (is_numeric($idpes_cad)) {
                    $this->idpes_cad = $idpes_cad;
        }
        if (is_numeric($idmun_pai)) {
                    $this->idmun_pai = $idmun_pai;
        }
        if (is_numeric($idmun)) {
                    $this->idmun = $idmun;
        }

        if (is_string($nome)) {
            $this->nome = $nome;
        }
        if (is_numeric($area_km2)) {
            $this->area_km2 = $area_km2;
        }
        if (is_numeric($idmreg)) {
            $this->idmreg = $idmreg;
        }
        if (is_numeric($idasmun)) {
            $this->idasmun = $idasmun;
        }
        if (is_numeric($cod_ibge)) {
            $this->cod_ibge = $cod_ibge;
        }
        if (is_string($geom)) {
            $this->geom = $geom;
        }
        if (is_string($tipo)) {
            $this->tipo = $tipo;
        }
        if (is_string($data_rev)) {
            $this->data_rev = $data_rev;
        }
        if (is_string($data_cad)) {
            $this->data_cad = $data_cad;
        }
        if (is_string($origem_gravacao)) {
            $this->origem_gravacao = $origem_gravacao;
        }
        if (is_string($operacao)) {
            $this->operacao = $operacao;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_string($this->nome) && is_string($this->sigla_uf) && is_string($this->tipo) && is_string($this->origem_gravacao) && is_string($this->operacao)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_string($this->nome)) {
                $campos .= "{$gruda}nome";
                $valores .= "{$gruda}E'" . pg_escape_string($this->nome) . '\'';
                $gruda = ', ';
            }
            if (is_string($this->sigla_uf)) {
                $campos .= "{$gruda}sigla_uf";
                $valores .= "{$gruda}'{$this->sigla_uf}'";
                $gruda = ', ';
            }
            if (is_numeric($this->area_km2)) {
                $campos .= "{$gruda}area_km2";
                $valores .= "{$gruda}'{$this->area_km2}'";
                $gruda = ', ';
            }
            if (is_numeric($this->idmreg)) {
                $campos .= "{$gruda}idmreg";
                $valores .= "{$gruda}'{$this->idmreg}'";
                $gruda = ', ';
            }
            if (is_numeric($this->idasmun)) {
                $campos .= "{$gruda}idasmun";
                $valores .= "{$gruda}'{$this->idasmun}'";
                $gruda = ', ';
            }
            if (is_numeric($this->cod_ibge)) {
                $campos .= "{$gruda}cod_ibge";
                $valores .= "{$gruda}'{$this->cod_ibge}'";
                $gruda = ', ';
            }
            if (is_string($this->geom)) {
                $campos .= "{$gruda}geom";
                $valores .= "{$gruda}'{$this->geom}'";
                $gruda = ', ';
            }
            if (is_string($this->tipo)) {
                $campos .= "{$gruda}tipo";
                $valores .= "{$gruda}'{$this->tipo}'";
                $gruda = ', ';
            }
            if (is_numeric($this->idmun_pai)) {
                $campos .= "{$gruda}idmun_pai";
                $valores .= "{$gruda}'{$this->idmun_pai}'";
                $gruda = ', ';
            }
            if (is_numeric($this->idpes_rev)) {
                $campos .= "{$gruda}idpes_rev";
                $valores .= "{$gruda}'{$this->idpes_rev}'";
                $gruda = ', ';
            }
            if (is_numeric($this->idpes_cad)) {
                $campos .= "{$gruda}idpes_cad";
                $valores .= "{$gruda}'{$this->idpes_cad}'";
                $gruda = ', ';
            }
            if (is_string($this->data_rev)) {
                $campos .= "{$gruda}data_rev";
                $valores .= "{$gruda}'{$this->data_rev}'";
                $gruda = ', ';
            }
            $campos .= "{$gruda}data_cad";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';
            if (is_string($this->origem_gravacao)) {
                $campos .= "{$gruda}origem_gravacao";
                $valores .= "{$gruda}'{$this->origem_gravacao}'";
                $gruda = ', ';
            }
            if (is_string($this->operacao)) {
                $campos .= "{$gruda}operacao";
                $valores .= "{$gruda}'{$this->operacao}'";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId('seq_municipio');
        }

        return false;
    }

    /**
     * Edita os dados de um registro
     *
     * @return bool
     */
    public function edita()
    {
        if (is_numeric($this->idmun)) {
            $db = new clsBanco();
            $set = '';
            $gruda = '';

            if (is_string($this->nome)) {
                $set .= "{$gruda}nome = E'" . pg_escape_string($this->nome) . '\'';
                $gruda = ', ';
            }
            if (is_string($this->sigla_uf)) {
                $set .= "{$gruda}sigla_uf = '{$this->sigla_uf}'";
                $gruda = ', ';
            }
            if (is_numeric($this->area_km2)) {
                $set .= "{$gruda}area_km2 = '{$this->area_km2}'";
                $gruda = ', ';
            }
            if (is_numeric($this->idmreg)) {
                $set .= "{$gruda}idmreg = '{$this->idmreg}'";
                $gruda = ', ';
            }
            if (is_numeric($this->idasmun)) {
                $set .= "{$gruda}idasmun = '{$this->idasmun}'";
                $gruda = ', ';
            }
            if (is_numeric($this->cod_ibge)) {
                $set .= "{$gruda}cod_ibge = '{$this->cod_ibge}'";
                $gruda = ', ';
            } elseif (is_null($this->cod_ibge)) {
                $set .= "{$gruda}cod_ibge = NULL";
                $gruda = ', ';
            }
            if (is_string($this->geom)) {
                $set .= "{$gruda}geom = '{$this->geom}'";
                $gruda = ', ';
            }
            if (is_string($this->tipo)) {
                $set .= "{$gruda}tipo = '{$this->tipo}'";
                $gruda = ', ';
            }
            if (is_numeric($this->idmun_pai)) {
                $set .= "{$gruda}idmun_pai = '{$this->idmun_pai}'";
                $gruda = ', ';
            }
            if (is_numeric($this->idpes_rev)) {
                $set .= "{$gruda}idpes_rev = '{$this->idpes_rev}'";
                $gruda = ', ';
            }
            if (is_numeric($this->idpes_cad)) {
                $set .= "{$gruda}idpes_cad = '{$this->idpes_cad}'";
                $gruda = ', ';
            }
            if (is_string($this->data_rev)) {
                $set .= "{$gruda}data_rev = '{$this->data_rev}'";
                $gruda = ', ';
            }
            if (is_string($this->data_cad)) {
                $set .= "{$gruda}data_cad = '{$this->data_cad}'";
                $gruda = ', ';
            }
            if (is_string($this->origem_gravacao)) {
                $set .= "{$gruda}origem_gravacao = '{$this->origem_gravacao}'";
                $gruda = ', ';
            }
            if (is_string($this->operacao)) {
                $set .= "{$gruda}operacao = '{$this->operacao}'";
                $gruda = ', ';
            }
            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE idmun = '{$this->idmun}'");

                return true;
            }
        }

        return false;
    }

    /**
     * Retorna uma lista filtrados de acordo com os parametros
     *
     * @param string str_nome
     * @param string str_sigla_uf
     * @param integer int_area_km2
     * @param integer int_idmreg
     * @param integer int_idasmun
     * @param integer int_cod_ibge
     * @param string str_geom
     * @param string str_tipo
     * @param integer int_idmun_pai
     * @param integer int_idpes_rev
     * @param integer int_idpes_cad
     * @param string date_data_rev_ini
     * @param string date_data_rev_fim
     * @param string date_data_cad_ini
     * @param string date_data_cad_fim
     * @param string str_origem_gravacao
     * @param string str_operacao
     *
     * @return array
     */
    public function lista($str_nome = null, $str_sigla_uf = null, $int_area_km2 = null, $int_idmreg = null, $int_idasmun = null, $int_cod_ibge = null, $str_geom = null, $str_tipo = null, $int_idmun_pai = null, $int_idpes_rev = null, $int_idpes_cad = null, $date_data_rev_ini = null, $date_data_rev_fim = null, $date_data_cad_ini = null, $date_data_cad_fim = null, $str_origem_gravacao = null, $str_operacao = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_idmun)) {
            $filtros .= "{$whereAnd} idmun = '{$int_idmun}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_nome)) {
            $filtros .= "{$whereAnd} UNACCENT(nome) ILIKE UNACCENT('%" . addslashes($str_nome) . '%\')';
            $whereAnd = ' AND ';
        }
        if (is_string($str_sigla_uf)) {
            $filtros .= "{$whereAnd} sigla_uf LIKE '%{$str_sigla_uf}%'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_area_km2)) {
            $filtros .= "{$whereAnd} area_km2 = '{$int_area_km2}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_idmreg)) {
            $filtros .= "{$whereAnd} idmreg = '{$int_idmreg}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_idasmun)) {
            $filtros .= "{$whereAnd} idasmun = '{$int_idasmun}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_cod_ibge)) {
            $filtros .= "{$whereAnd} cod_ibge = '{$int_cod_ibge}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_geom)) {
            $filtros .= "{$whereAnd} geom LIKE '%{$str_geom}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_tipo)) {
            $filtros .= "{$whereAnd} tipo LIKE '%{$str_tipo}%'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_idmun_pai)) {
            $filtros .= "{$whereAnd} idmun_pai = '{$int_idmun_pai}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_idpes_rev)) {
            $filtros .= "{$whereAnd} idpes_rev = '{$int_idpes_rev}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_idpes_cad)) {
            $filtros .= "{$whereAnd} idpes_cad = '{$int_idpes_cad}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_rev_ini)) {
            $filtros .= "{$whereAnd} data_rev >= '{$date_data_rev_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_rev_fim)) {
            $filtros .= "{$whereAnd} data_rev <= '{$date_data_rev_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cad_ini)) {
            $filtros .= "{$whereAnd} data_cad >= '{$date_data_cad_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cad_fim)) {
            $filtros .= "{$whereAnd} data_cad <= '{$date_data_cad_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_origem_gravacao)) {
            $filtros .= "{$whereAnd} origem_gravacao LIKE '%{$str_origem_gravacao}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_operacao)) {
            $filtros .= "{$whereAnd} operacao LIKE '%{$str_operacao}%'";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} {$filtros}");

        $db->Consulta($sql);

        if ($countCampos > 1) {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();

                $tupla['_total'] = $this->_total;
                $resultado[] = $tupla;
            }
        } else {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();
                $resultado[] = $tupla[$this->_campos_lista];
            }
        }
        if (count($resultado)) {
            return $resultado;
        }

        return false;
    }

    /**
     * Retorna um array com os dados de um registro
     *
     * @return array
     */
    public function detalhe()
    {
        if (is_numeric($this->idmun)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE idmun = '{$this->idmun}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Retorna true se o registro existir. Caso contrário retorna false.
     *
     * @return bool
     */
    public function existe()
    {
        if (is_numeric($this->idmun)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE idmun = '{$this->idmun}'");
            if ($db->ProximoRegistro()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Exclui um registro
     *
     * @return bool
     */
    public function excluir()
    {
        if (is_numeric($this->idmun)) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->_tabela} WHERE idmun = '{$this->idmun}'");

            return true;
        }

        return false;
    }
}
