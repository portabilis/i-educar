<?php

use iEducar\Legacy\Model;

class clsPmieducarInfraPredioComodo extends Model
{
    public $cod_infra_predio_comodo;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_cod_infra_comodo_funcao;
    public $ref_cod_infra_predio;
    public $nm_comodo;
    public $desc_comodo;
    public $area;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $codUsuario;

    public function __construct($cod_infra_predio_comodo = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $ref_cod_infra_comodo_funcao = null, $ref_cod_infra_predio = null, $nm_comodo = null, $desc_comodo = null, $area = null, $data_cadastro = null, $data_exclusao = null, $ativo = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}infra_predio_comodo";

        $this->_campos_lista = $this->_todos_campos = 'ipc.cod_infra_predio_comodo, ipc.ref_usuario_exc, ipc.ref_usuario_cad, ipc.ref_cod_infra_comodo_funcao, ipc.ref_cod_infra_predio, ipc.nm_comodo, ipc.desc_comodo, ipc.area, ipc.data_cadastro, ipc.data_exclusao, ipc.ativo';

        if (is_numeric($ref_cod_infra_comodo_funcao)) {
            $this->ref_cod_infra_comodo_funcao = $ref_cod_infra_comodo_funcao;
        }
        if (is_numeric($ref_usuario_exc)) {
            $this->ref_usuario_exc = $ref_usuario_exc;
        }
        if (is_numeric($ref_usuario_cad)) {
            $this->ref_usuario_cad = $ref_usuario_cad;
        }
        if (is_numeric($ref_cod_infra_predio)) {
            $this->ref_cod_infra_predio = $ref_cod_infra_predio;
        }

        if (is_numeric($cod_infra_predio_comodo)) {
            $this->cod_infra_predio_comodo = $cod_infra_predio_comodo;
        }
        if (is_string($nm_comodo)) {
            $this->nm_comodo = $nm_comodo;
        }
        if (is_string($desc_comodo)) {
            $this->desc_comodo = $desc_comodo;
        }
        if (is_numeric($area)) {
            $this->area = $area;
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
        if (is_numeric($this->ref_usuario_cad) && is_numeric($this->ref_cod_infra_comodo_funcao) && is_numeric($this->ref_cod_infra_predio) && is_string($this->nm_comodo) && is_numeric($this->area)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_usuario_cad)) {
                $campos .= "{$gruda}ref_usuario_cad";
                $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_infra_comodo_funcao)) {
                $campos .= "{$gruda}ref_cod_infra_comodo_funcao";
                $valores .= "{$gruda}'{$this->ref_cod_infra_comodo_funcao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_infra_predio)) {
                $campos .= "{$gruda}ref_cod_infra_predio";
                $valores .= "{$gruda}'{$this->ref_cod_infra_predio}'";
                $gruda = ', ';
            }
            if (is_string($this->nm_comodo)) {
                $campos .= "{$gruda}nm_comodo";
                $valores .= "{$gruda}'{$this->nm_comodo}'";
                $gruda = ', ';
            }
            if (is_string($this->desc_comodo)) {
                $campos .= "{$gruda}desc_comodo";
                $valores .= "{$gruda}'{$this->desc_comodo}'";
                $gruda = ', ';
            }
            if (is_numeric($this->area)) {
                $campos .= "{$gruda}area";
                $valores .= "{$gruda}'{$this->area}'";
                $gruda = ', ';
            }
            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';
            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId("{$this->_tabela}_cod_infra_predio_comodo_seq");
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
        if (is_numeric($this->cod_infra_predio_comodo) && is_numeric($this->ref_usuario_exc)) {
            $db = new clsBanco();
            $set = '';

            if (is_numeric($this->ref_usuario_exc)) {
                $set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_cad)) {
                $set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_infra_comodo_funcao)) {
                $set .= "{$gruda}ref_cod_infra_comodo_funcao = '{$this->ref_cod_infra_comodo_funcao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_infra_predio)) {
                $set .= "{$gruda}ref_cod_infra_predio = '{$this->ref_cod_infra_predio}'";
                $gruda = ', ';
            }
            if (is_string($this->nm_comodo)) {
                $set .= "{$gruda}nm_comodo = '{$this->nm_comodo}'";
                $gruda = ', ';
            }
            if (is_string($this->desc_comodo)) {
                $set .= "{$gruda}desc_comodo = '{$this->desc_comodo}'";
                $gruda = ', ';
            }
            if (is_numeric($this->area)) {
                $set .= "{$gruda}area = '{$this->area}'";
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
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_infra_predio_comodo = '{$this->cod_infra_predio_comodo}'");

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
    public function lista($int_cod_infra_predio_comodo = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $int_ref_cod_infra_comodo_funcao = null, $int_ref_cod_infra_predio = null, $str_nm_comodo = null, $str_desc_comodo = null, $int_area = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $int_ref_cod_escola = null, $int_ref_cod_instituicao = null)
    {
        $sql = "SELECT {$this->_campos_lista}, ip.ref_cod_escola, e.ref_cod_instituicao FROM {$this->_tabela} ipc, {$this->_schema}infra_predio ip, {$this->_schema}escola e ";

        $whereAnd = ' AND ';
        $filtros = ' WHERE ipc.ref_cod_infra_predio = ip.cod_infra_predio AND ip.ref_cod_escola = e.cod_escola ';

        if (is_numeric($int_cod_infra_predio_comodo)) {
            $filtros .= "{$whereAnd} ipc.cod_infra_predio_comodo = '{$int_cod_infra_predio_comodo}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_exc)) {
            $filtros .= "{$whereAnd} ipc.ref_usuario_exc = '{$int_ref_usuario_exc}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_cad)) {
            $filtros .= "{$whereAnd} ipc.ref_usuario_cad = '{$int_ref_usuario_cad}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_infra_comodo_funcao)) {
            $filtros .= "{$whereAnd} ipc.ref_cod_infra_comodo_funcao = '{$int_ref_cod_infra_comodo_funcao}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_infra_predio)) {
            $filtros .= "{$whereAnd} ipc.ref_cod_infra_predio = '{$int_ref_cod_infra_predio}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_nm_comodo)) {
            $filtros .= "{$whereAnd} ipc.nm_comodo LIKE '%{$str_nm_comodo}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_desc_comodo)) {
            $filtros .= "{$whereAnd} ipc.desc_comodo LIKE '%{$str_desc_comodo}%'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_area)) {
            $filtros .= "{$whereAnd} ipc.area = '{$int_area}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_ini)) {
            $filtros .= "{$whereAnd} ipc.data_cadastro >= '{$date_data_cadastro_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_fim)) {
            $filtros .= "{$whereAnd} ipc.data_cadastro <= '{$date_data_cadastro_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_ini)) {
            $filtros .= "{$whereAnd} ipc.data_exclusao >= '{$date_data_exclusao_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_fim)) {
            $filtros .= "{$whereAnd} ipc.data_exclusao <= '{$date_data_exclusao_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_null($int_ativo) || $int_ativo) {
            $filtros .= "{$whereAnd} ipc.ativo = '1'";
            $whereAnd = ' AND ';
        } else {
            $filtros .= "{$whereAnd} ipc.ativo = '0'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_escola)) {
            $filtros .= "{$whereAnd} ip.ref_cod_escola = {$int_ref_cod_escola} ";
            $whereAnd = ' AND ';
        } elseif ($this->codUsuario) {
            $filtros .= "{$whereAnd} EXISTS (SELECT 1
                                               FROM pmieducar.escola_usuario
                                              WHERE escola_usuario.ref_cod_escola = ip.ref_cod_escola
                                                AND escola_usuario.ref_cod_usuario = '{$this->codUsuario}')";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_instituicao)) {
            $filtros .= "{$whereAnd} e.ref_cod_instituicao = {$int_ref_cod_instituicao} ";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} ipc, {$this->_schema}infra_predio ip, {$this->_schema}escola e {$filtros}");

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
        if (is_numeric($this->cod_infra_predio_comodo)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} ipc WHERE ipc.cod_infra_predio_comodo = '{$this->cod_infra_predio_comodo}'");
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
        if (is_numeric($this->cod_infra_predio_comodo)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_infra_predio_comodo = '{$this->cod_infra_predio_comodo}'");
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
        if (is_numeric($this->cod_infra_predio_comodo) && is_numeric($this->ref_usuario_exc)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }
}
