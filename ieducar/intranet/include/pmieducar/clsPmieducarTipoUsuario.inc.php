<?php

use iEducar\Legacy\Model;

require_once 'include/pmieducar/geral.inc.php';

class clsPmieducarTipoUsuario extends Model
{
    public $cod_tipo_usuario;
    public $ref_funcionario_cad;
    public $ref_funcionario_exc;
    public $nm_tipo;
    public $descricao;
    public $nivel;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function __construct(
        $cod_tipo_usuario = null,
        $ref_funcionario_cad = null,
        $ref_funcionario_exc = null,
        $nm_tipo = null,
        $descricao = null,
        $nivel = null,
        $data_cadastro = null,
        $data_exclusao = null,
        $ativo = null
    ) {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}tipo_usuario";

        $this->_campos_lista = $this->_todos_campos = 'cod_tipo_usuario, ref_funcionario_cad, ref_funcionario_exc, nm_tipo, descricao, nivel, data_cadastro, data_exclusao, ativo';

        if (is_numeric($ref_funcionario_exc)) {
            $this->ref_funcionario_exc = $ref_funcionario_exc;
        }
        if (is_numeric($ref_funcionario_cad)) {
            $this->ref_funcionario_cad = $ref_funcionario_cad;
        }
        if (is_numeric($cod_tipo_usuario)) {
            $this->cod_tipo_usuario = $cod_tipo_usuario;
        }
        if (is_string($nm_tipo)) {
            $this->nm_tipo = $nm_tipo;
        }
        if (is_string($descricao)) {
            $this->descricao = $descricao;
        }
        if (is_numeric($nivel)) {
            $this->nivel = $nivel;
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
        if (is_numeric($this->ref_funcionario_cad) && is_string($this->nm_tipo) && is_numeric($this->nivel) && is_numeric($this->ativo)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_funcionario_cad)) {
                $campos .= "{$gruda}ref_funcionario_cad";
                $valores .= "{$gruda}'{$this->ref_funcionario_cad}'";
                $gruda = ', ';
            }
            if (is_string($this->nm_tipo)) {
                $campos .= "{$gruda}nm_tipo";
                $valores .= "{$gruda}'{$this->nm_tipo}'";
                $gruda = ', ';
            }
            if (is_string($this->descricao)) {
                $campos .= "{$gruda}descricao";
                $valores .= "{$gruda}'{$this->descricao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->nivel)) {
                $campos .= "{$gruda}nivel";
                $valores .= "{$gruda}'{$this->nivel}'";
                $gruda = ', ';
            }
            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';
            if (is_numeric($this->ativo)) {
                $campos .= "{$gruda}ativo";
                $valores .= "{$gruda}'{$this->ativo}'";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId("{$this->_tabela}_cod_tipo_usuario_seq");
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
        if (is_numeric($this->cod_tipo_usuario) && is_numeric($this->ref_funcionario_exc)) {
            $db = new clsBanco();
            $set = '';

            if (is_numeric($this->ref_funcionario_cad)) {
                $set .= "{$gruda}ref_funcionario_cad = '{$this->ref_funcionario_cad}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_funcionario_exc)) {
                $set .= "{$gruda}ref_funcionario_exc = '{$this->ref_funcionario_exc}'";
                $gruda = ', ';
            }
            if (is_string($this->nm_tipo)) {
                $set .= "{$gruda}nm_tipo = '{$this->nm_tipo}'";
                $gruda = ', ';
            }
            if (is_string($this->descricao)) {
                $set .= "{$gruda}descricao = '{$this->descricao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->nivel)) {
                $set .= "{$gruda}nivel = '{$this->nivel}'";
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
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_tipo_usuario = '{$this->cod_tipo_usuario}'");

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
    public function lista(
        $int_cod_tipo_usuario = null,
        $int_ref_funcionario_cad = null,
        $int_ref_funcionario_exc = null,
        $str_nm_tipo = null,
        $str_descricao = null,
        $int_nivel = null,
        $date_data_cadastro = null,
        $date_data_exclusao = null,
        $int_ativo = null,
        $int_nivel_menor = null
    ) {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_cod_tipo_usuario)) {
            $filtros .= "{$whereAnd} cod_tipo_usuario = '{$int_cod_tipo_usuario}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_funcionario_cad)) {
            $filtros .= "{$whereAnd} ref_funcionario_cad = '{$int_ref_funcionario_cad}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_funcionario_exc)) {
            $filtros .= "{$whereAnd} ref_funcionario_exc = '{$int_ref_funcionario_exc}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_nm_tipo)) {
            $filtros .= "{$whereAnd} nm_tipo LIKE '%{$str_nm_tipo}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_descricao)) {
            $filtros .= "{$whereAnd} descricao LIKE '%{$str_descricao}%'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_nivel)) {
            $filtros .= "{$whereAnd} nivel = '{$int_nivel}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_nivel_menor)) {
            $filtros .= "{$whereAnd} nivel >= '{$int_nivel_menor}'";
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
        if (is_numeric($this->cod_tipo_usuario)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_tipo_usuario = '{$this->cod_tipo_usuario}'");
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
        if (is_numeric($this->cod_tipo_usuario)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_tipo_usuario = '{$this->cod_tipo_usuario}'");
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
        if (!is_numeric($this->cod_tipo_usuario)) {
            return false;
        }

        if (!is_numeric($this->ref_funcionario_exc)) {
            return false;
        }

        $this->ativo = 0;

        return $this->edita();
    }

    public function possuiUsuarioRelacionado()
    {
        $db = new clsBanco();
        $resultado = $db->CampoUnico("SELECT 1 FROM pmieducar.usuario WHERE ref_cod_tipo_usuario = {$this->cod_tipo_usuario}");

        return (bool) $resultado;
    }
}
