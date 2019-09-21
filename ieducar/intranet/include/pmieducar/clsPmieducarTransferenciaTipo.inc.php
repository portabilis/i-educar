<?php

use iEducar\Legacy\Model;

require_once 'include/pmieducar/geral.inc.php';

class clsPmieducarTransferenciaTipo extends Model
{
    public $cod_transferencia_tipo;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_tipo;
    public $desc_tipo;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_instituicao;

    public function __construct($cod_transferencia_tipo = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $nm_tipo = null, $desc_tipo = null, $data_cadastro = null, $data_exclusao = null, $ativo = null, $ref_cod_instituicao = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}transferencia_tipo";

        $this->_campos_lista = $this->_todos_campos = 'tt.cod_transferencia_tipo, tt.ref_usuario_exc, tt.ref_usuario_cad, tt.nm_tipo, tt.desc_tipo, tt.data_cadastro, tt.data_exclusao, tt.ativo, tt.ref_cod_instituicao';

        if (is_numeric($ref_cod_instituicao)) {
                    $this->ref_cod_instituicao = $ref_cod_instituicao;
        }
        if (is_numeric($ref_usuario_exc)) {
                    $this->ref_usuario_exc = $ref_usuario_exc;
        }
        if (is_numeric($ref_usuario_cad)) {
                    $this->ref_usuario_cad = $ref_usuario_cad;
        }

        if (is_numeric($cod_transferencia_tipo)) {
            $this->cod_transferencia_tipo = $cod_transferencia_tipo;
        }
        if (is_string($nm_tipo)) {
            $this->nm_tipo = $nm_tipo;
        }
        if (is_string($desc_tipo)) {
            $this->desc_tipo = $desc_tipo;
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
        if (is_numeric($this->ref_usuario_cad) && is_string($this->nm_tipo) && is_numeric($this->ref_cod_instituicao)) {
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
            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';
            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';
            if (is_numeric($this->ref_cod_instituicao)) {
                $campos .= "{$gruda}ref_cod_instituicao";
                $valores .= "{$gruda}'{$this->ref_cod_instituicao}'";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId("{$this->_tabela}_cod_transferencia_tipo_seq");
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
        if (is_numeric($this->cod_transferencia_tipo) && is_numeric($this->ref_usuario_exc)) {
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
            if (is_string($this->nm_tipo)) {
                $set .= "{$gruda}nm_tipo = '{$this->nm_tipo}'";
                $gruda = ', ';
            }
            if (is_string($this->desc_tipo)) {
                $set .= "{$gruda}desc_tipo = '{$this->desc_tipo}'";
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
            if (is_numeric($this->ref_cod_instituicao)) {
                $set .= "{$gruda}ref_cod_instituicao = '{$this->ref_cod_instituicao}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_transferencia_tipo = '{$this->cod_transferencia_tipo}'");

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
    public function lista($int_cod_transferencia_tipo = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $str_nm_tipo = null, $str_desc_tipo = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $int_ref_cod_instituicao = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela} tt";

        $whereAnd = ' WHERE ';
        $filtros = '';

        if (is_numeric($int_cod_transferencia_tipo)) {
            $filtros .= "{$whereAnd} tt.cod_transferencia_tipo = '{$int_cod_transferencia_tipo}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_exc)) {
            $filtros .= "{$whereAnd} tt.ref_usuario_exc = '{$int_ref_usuario_exc}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_cad)) {
            $filtros .= "{$whereAnd} tt.ref_usuario_cad = '{$int_ref_usuario_cad}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_nm_tipo)) {
            $filtros .= "{$whereAnd} tt.nm_tipo LIKE '%{$str_nm_tipo}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_desc_tipo)) {
            $filtros .= "{$whereAnd} tt.desc_tipo LIKE '%{$str_desc_tipo}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_ini)) {
            $filtros .= "{$whereAnd} tt.data_cadastro >= '{$date_data_cadastro_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_fim)) {
            $filtros .= "{$whereAnd} tt.data_cadastro <= '{$date_data_cadastro_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_ini)) {
            $filtros .= "{$whereAnd} tt.data_exclusao >= '{$date_data_exclusao_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_fim)) {
            $filtros .= "{$whereAnd} tt.data_exclusao <= '{$date_data_exclusao_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_null($int_ativo) || $int_ativo) {
            $filtros .= "{$whereAnd} tt.ativo = '1'";
            $whereAnd = ' AND ';
        } else {
            $filtros .= "{$whereAnd} tt.ativo = '0'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_instituicao)) {
            $filtros .= "{$whereAnd} ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} tt {$filtros}");

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
        if (is_numeric($this->cod_transferencia_tipo)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} tt WHERE tt.cod_transferencia_tipo = '{$this->cod_transferencia_tipo}'");
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
        if (is_numeric($this->cod_transferencia_tipo)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_transferencia_tipo = '{$this->cod_transferencia_tipo}'");
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
        if (is_numeric($this->cod_transferencia_tipo) && is_numeric($this->ref_usuario_exc)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }
}
