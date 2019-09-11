<?php

use iEducar\Legacy\Model;

class clsCadastroReligiao extends Model
{
    public $cod_religiao;
    public $idpes_exc;
    public $idpes_cad;
    public $nm_religiao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    /**
     * Construtor (PHP 4)
     *
     * @param integer cod_religiao
     * @param integer idpes_exc
     * @param integer idpes_cad
     * @param string nm_religiao
     * @param string data_cadastro
     * @param string data_exclusao
     * @param bool ativo
     *
     * @return object
     */
    public function __construct($cod_religiao = null, $idpes_exc = null, $idpes_cad = null, $nm_religiao = null, $data_cadastro = null, $data_exclusao = null, $ativo = null)
    {
        $db = new clsBanco();
        $this->_schema = 'cadastro.';
        $this->_tabela = "{$this->_schema}religiao";

        $this->_campos_lista = $this->_todos_campos = 'cod_religiao, idpes_exc, idpes_cad, nm_religiao, data_cadastro, data_exclusao, ativo';

        if (is_numeric($idpes_exc)) {
                    $this->idpes_exc = $idpes_exc;
        }
        if (is_numeric($idpes_cad)) {
                    $this->idpes_cad = $idpes_cad;
        }

        if (is_numeric($cod_religiao)) {
            $this->cod_religiao = $cod_religiao;
        }
        if (is_string($nm_religiao)) {
            $this->nm_religiao = $nm_religiao;
        }
        if (is_string($data_cadastro)) {
            $this->data_cadastro = $data_cadastro;
        }
        if (is_string($data_exclusao)) {
            $this->data_exclusao = $data_exclusao;
        }
        if (! is_null($ativo)) {
            $this->ativo = $ativo;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->idpes_cad) && is_string($this->nm_religiao)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->idpes_exc)) {
                $campos .= "{$gruda}idpes_exc";
                $valores .= "{$gruda}'{$this->idpes_exc}'";
                $gruda = ', ';
            }
            if (is_numeric($this->idpes_cad)) {
                $campos .= "{$gruda}idpes_cad";
                $valores .= "{$gruda}'{$this->idpes_cad}'";
                $gruda = ', ';
            }
            if (is_string($this->nm_religiao)) {
                $campos .= "{$gruda}nm_religiao";
                $valores .= "{$gruda}'{$this->nm_religiao}'";
                $gruda = ', ';
            }
            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';
            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId("{$this->_tabela}_cod_religiao_seq");
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
        if (is_numeric($this->cod_religiao)) {
            $db = new clsBanco();
            $set = '';

            if (is_numeric($this->idpes_exc)) {
                $set .= "{$gruda}idpes_exc = '{$this->idpes_exc}'";
                $gruda = ', ';
            }
            if (is_numeric($this->idpes_cad)) {
                $set .= "{$gruda}idpes_cad = '{$this->idpes_cad}'";
                $gruda = ', ';
            }
            if (is_string($this->nm_religiao)) {
                $set .= "{$gruda}nm_religiao = '{$this->nm_religiao}'";
                $gruda = ', ';
            }
            if (is_string($this->data_cadastro)) {
                $set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
                $gruda = ', ';
            }
            $set .= "{$gruda}data_exclusao = NOW()";
            $gruda = ', ';
            if (! is_null($this->ativo)) {
                $val = dbBool($this->ativo) ? 'TRUE': 'FALSE';
                $set .= "{$gruda}ativo = {$val}";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_religiao = '{$this->cod_religiao}'");

                return true;
            }
        }

        return false;
    }

    /**
     * Retorna uma lista filtrados de acordo com os parametros
     *
     * @param integer int_idpes_exc
     * @param integer int_idpes_cad
     * @param string str_nm_religiao
     * @param string date_data_cadastro_ini
     * @param string date_data_cadastro_fim
     * @param string date_data_exclusao_ini
     * @param string date_data_exclusao_fim
     * @param bool bool_ativo
     *
     * @return array
     */
    public function lista($int_idpes_exc = null, $int_idpes_cad = null, $str_nm_religiao = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $bool_ativo = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_cod_religiao)) {
            $filtros .= "{$whereAnd} cod_religiao = '{$int_cod_religiao}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_idpes_exc)) {
            $filtros .= "{$whereAnd} idpes_exc = '{$int_idpes_exc}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_idpes_cad)) {
            $filtros .= "{$whereAnd} idpes_cad = '{$int_idpes_cad}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_nm_religiao)) {
            $filtros .= "{$whereAnd} nm_religiao LIKE '%{$str_nm_religiao}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_ini)) {
            $filtros .= "{$whereAnd} data_cadastro >= '{$date_data_cadastro_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_fim)) {
            $filtros .= "{$whereAnd} data_cadastro <= '{$date_data_cadastro_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_ini)) {
            $filtros .= "{$whereAnd} data_exclusao >= '{$date_data_exclusao_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_fim)) {
            $filtros .= "{$whereAnd} data_exclusao <= '{$date_data_exclusao_fim}'";
            $whereAnd = ' AND ';
        }
        if (! is_null($bool_ativo)) {
            if (dbBool($bool_ativo)) {
                $filtros .= "{$whereAnd} ativo = TRUE";
            } else {
                $filtros .= "{$whereAnd} ativo = FALSE";
            }
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
        if (is_numeric($this->cod_religiao)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_religiao = '{$this->cod_religiao}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Retorna um array com os dados de um registro
     *
     * @return array
     */
    public function existe()
    {
        if (is_numeric($this->cod_religiao)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_religiao = '{$this->cod_religiao}'");
            $db->ProximoRegistro();

            return $db->Tupla();
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
        if (is_numeric($this->cod_religiao)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }
}
