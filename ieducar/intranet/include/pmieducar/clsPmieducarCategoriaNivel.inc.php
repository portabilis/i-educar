<?php

use iEducar\Legacy\Model;

class clsPmieducarCategoriaNivel extends Model
{
    public $cod_categoria_nivel;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_categoria_nivel;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function __construct($cod_categoria_nivel = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $nm_categoria_nivel = null, $data_cadastro = null, $data_exclusao = null, $ativo = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}categoria_nivel";

        $this->_campos_lista = $this->_todos_campos = 'cod_categoria_nivel, ref_usuario_exc, ref_usuario_cad, nm_categoria_nivel, data_cadastro, data_exclusao, ativo';

        if (is_numeric($ref_usuario_cad)) {
            $this->ref_usuario_cad = $ref_usuario_cad;
        }
        if (is_numeric($ref_usuario_exc)) {
            $this->ref_usuario_exc = $ref_usuario_exc;
        }

        if (is_numeric($cod_categoria_nivel)) {
            $this->cod_categoria_nivel = $cod_categoria_nivel;
        }
        if (is_string($nm_categoria_nivel)) {
            $this->nm_categoria_nivel = $nm_categoria_nivel;
        }
        if (is_string($data_cadastro)) {
            $this->data_cadastro = $data_cadastro;
        }
        if (is_string($data_exclusao)) {
            $this->data_exclusao = $data_exclusao;
        }
        if (!is_null($ativo)) {
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
        if (is_numeric($this->ref_usuario_cad) && is_string($this->nm_categoria_nivel)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_usuario_cad)) {
                $campos .= "{$gruda}ref_usuario_cad";
                $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_string($this->nm_categoria_nivel)) {
                $nm_categoria_nivel = $db->escapeString($this->nm_categoria_nivel);
                $campos .= "{$gruda}nm_categoria_nivel";
                $valores .= "{$gruda}'{$nm_categoria_nivel}'";
                $gruda = ', ';
            }
            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';
            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId("{$this->_tabela}_cod_categoria_nivel_seq");
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
        if (is_numeric($this->cod_categoria_nivel) && is_numeric($this->ref_usuario_exc)) {
            $db = new clsBanco();
            $gruda = '';
            $set = '';

            if (is_numeric($this->ref_usuario_exc)) {
                $set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_cad)) {
                $set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_string($this->nm_categoria_nivel)) {
                $nm_categoria_nivel = $db->escapeString($this->nm_categoria_nivel);
                $set .= "{$gruda}nm_categoria_nivel = '{$nm_categoria_nivel}'";
                $gruda = ', ';
            }
            if (is_string($this->data_cadastro)) {
                $set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
                $gruda = ', ';
            }
            $set .= "{$gruda}data_exclusao = NOW()";
            $gruda = ', ';
            if (!is_null($this->ativo)) {
                $val = dbBool($this->ativo) ? 'TRUE' : 'FALSE';
                $set .= "{$gruda}ativo = {$val}";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_categoria_nivel = '{$this->cod_categoria_nivel}'");

                return true;
            }
        }

        return false;
    }

    /**
     * Retorna uma lista filtrados de acordo com os parametros
     *
     * @param integer int_ref_usuario_exc
     * @param integer int_ref_usuario_cad
     * @param string str_nm_categoria_nivel
     * @param string date_data_cadastro_ini
     * @param string date_data_cadastro_fim
     * @param string date_data_exclusao_ini
     * @param string date_data_exclusao_fim
     * @param bool bool_ativo
     *
     * @return array
     */
    public function lista($int_cod_categoria_nivel = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $str_nm_categoria_nivel = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $bool_ativo = null)
    {
        $db = new clsBanco();

        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_cod_categoria_nivel)) {
            $filtros .= "{$whereAnd} cod_categoria_nivel = '{$int_cod_categoria_nivel}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_exc)) {
            $filtros .= "{$whereAnd} ref_usuario_exc = '{$int_ref_usuario_exc}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_cad)) {
            $filtros .= "{$whereAnd} ref_usuario_cad = '{$int_ref_usuario_cad}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_nm_categoria_nivel)) {
            $str_nome_categoria_nivel = $db->escapeString($str_nm_categoria_nivel);
            $filtros .= "{$whereAnd} nm_categoria_nivel LIKE '%{$str_nome_categoria_nivel}%'";
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
        if (!is_null($bool_ativo)) {
            if (dbBool($bool_ativo)) {
                $filtros .= "{$whereAnd} ativo = TRUE";
            } else {
                $filtros .= "{$whereAnd} ativo = FALSE";
            }
            $whereAnd = ' AND ';
        }

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
        if (is_numeric($this->cod_categoria_nivel)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_categoria_nivel = '{$this->cod_categoria_nivel}'");
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
        if (is_numeric($this->cod_categoria_nivel)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_categoria_nivel = '{$this->cod_categoria_nivel}'");
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
        if (is_numeric($this->cod_categoria_nivel) && is_numeric($this->ref_usuario_exc)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }
}
