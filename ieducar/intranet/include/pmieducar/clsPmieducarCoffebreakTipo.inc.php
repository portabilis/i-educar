<?php

use iEducar\Legacy\Model;

class clsPmieducarCoffebreakTipo extends Model
{
    public $cod_coffebreak_tipo;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_tipo;
    public $desc_tipo;
    public $custo_unitario;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function __construct($cod_coffebreak_tipo = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $nm_tipo = null, $desc_tipo = null, $custo_unitario = null, $data_cadastro = null, $data_exclusao = null, $ativo = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}coffebreak_tipo";

        $this->_campos_lista = $this->_todos_campos = 'cod_coffebreak_tipo, ref_usuario_exc, ref_usuario_cad, nm_tipo, desc_tipo, custo_unitario, data_cadastro, data_exclusao, ativo';

        if (is_numeric($ref_usuario_cad)) {
            $this->ref_usuario_cad = $ref_usuario_cad;
        }
        if (is_numeric($ref_usuario_exc)) {
            $this->ref_usuario_exc = $ref_usuario_exc;
        }

        if (is_numeric($cod_coffebreak_tipo)) {
            $this->cod_coffebreak_tipo = $cod_coffebreak_tipo;
        }
        if (is_string($nm_tipo)) {
            $this->nm_tipo = $nm_tipo;
        }
        if (is_string($desc_tipo)) {
            $this->desc_tipo = $desc_tipo;
        }
        if (is_numeric($custo_unitario)) {
            $this->custo_unitario = $custo_unitario;
        }
        if (is_string($data_cadastro)) {
            $this->data_cadastro = $data_cadastro;
        }
        if (is_string($data_exclusao)) {
            $this->data_exclusao = $data_exclusao;
        }
        if (is_numeric($ativo)) {
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
        if (is_numeric($this->ref_usuario_cad) && is_string($this->nm_tipo) && is_numeric($this->custo_unitario)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_usuario_cad)) {
                $campos .= "{$gruda}ref_usuario_cad";
                $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_string($this->nm_tipo)) {
                $campos .= "{$gruda}nm_tipo";
                $valores .= "{$gruda}'{$this->nm_tipo}'";
                $gruda = ', ';
            }
            if (is_string($this->desc_tipo)) {
                $campos .= "{$gruda}desc_tipo";
                $valores .= "{$gruda}'{$this->desc_tipo}'";
                $gruda = ', ';
            }
            if (is_numeric($this->custo_unitario)) {
                $campos .= "{$gruda}custo_unitario";
                $valores .= "{$gruda}'{$this->custo_unitario}'";
                $gruda = ', ';
            }
            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';
            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId("{$this->_tabela}_cod_coffebreak_tipo_seq");
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
        if (is_numeric($this->cod_coffebreak_tipo) && is_numeric($this->ref_usuario_exc)) {
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
            if (is_string($this->nm_tipo)) {
                $set .= "{$gruda}nm_tipo = '{$this->nm_tipo}'";
                $gruda = ', ';
            }
            if (is_string($this->desc_tipo)) {
                $set .= "{$gruda}desc_tipo = '{$this->desc_tipo}'";
                $gruda = ', ';
            }
            if (is_numeric($this->custo_unitario)) {
                $set .= "{$gruda}custo_unitario = '{$this->custo_unitario}'";
                $gruda = ', ';
            }
            if (is_string($this->data_cadastro)) {
                $set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
                $gruda = ', ';
            }
            $set .= "{$gruda}data_exclusao = NOW()";
            $gruda = ', ';
            if (is_numeric($this->ativo)) {
                $set .= "{$gruda}ativo = '{$this->ativo}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_coffebreak_tipo = '{$this->cod_coffebreak_tipo}'");

                return true;
            }
        }

        return false;
    }

    /**
     * Retorna uma lista filtrados de acordo com os parametros
     *
     * @return array
     */
    public function lista($int_cod_coffebreak_tipo = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $str_nm_tipo = null, $str_desc_tipo = null, $int_custo_unitario = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_cod_coffebreak_tipo)) {
            $filtros .= "{$whereAnd} cod_coffebreak_tipo = '{$int_cod_coffebreak_tipo}'";
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
        if (is_string($str_nm_tipo)) {
            $filtros .= "{$whereAnd} nm_tipo LIKE '%{$str_nm_tipo}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_desc_tipo)) {
            $filtros .= "{$whereAnd} desc_tipo LIKE '%{$str_desc_tipo}%'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_custo_unitario)) {
            $filtros .= "{$whereAnd} custo_unitario = '{$int_custo_unitario}'";
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
        if (is_numeric($int_ativo)) {
            $filtros .= "{$whereAnd} ativo = '{$int_ativo}'";
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
        if (is_numeric($this->cod_coffebreak_tipo)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_coffebreak_tipo = '{$this->cod_coffebreak_tipo}'");
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
        if (is_numeric($this->cod_coffebreak_tipo)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_coffebreak_tipo = '{$this->cod_coffebreak_tipo}'");
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
        if (is_numeric($this->cod_coffebreak_tipo) && is_numeric($this->ref_usuario_exc)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }
}
