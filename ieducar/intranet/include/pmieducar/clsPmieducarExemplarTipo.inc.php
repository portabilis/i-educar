<?php

use iEducar\Legacy\Model;

class clsPmieducarExemplarTipo extends Model
{
    public $cod_exemplar_tipo;
    public $ref_cod_biblioteca;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_tipo;
    public $descricao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function __construct($cod_exemplar_tipo = null, $ref_cod_biblioteca = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $nm_tipo = null, $descricao = null, $data_cadastro = null, $data_exclusao = null, $ativo = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}exemplar_tipo";

        $this->_campos_lista = $this->_todos_campos = 'et.cod_exemplar_tipo, et.ref_cod_biblioteca, et.ref_usuario_exc, et.ref_usuario_cad, et.nm_tipo, et.descricao, et.data_cadastro, et.data_exclusao, et.ativo';

        if (is_numeric($ref_cod_biblioteca)) {
            $this->ref_cod_biblioteca = $ref_cod_biblioteca;
        }
        if (is_numeric($ref_usuario_exc)) {
            $this->ref_usuario_exc = $ref_usuario_exc;
        }
        if (is_numeric($ref_usuario_cad)) {
            $this->ref_usuario_cad = $ref_usuario_cad;
        }

        if (is_numeric($cod_exemplar_tipo)) {
            $this->cod_exemplar_tipo = $cod_exemplar_tipo;
        }
        if (is_string($nm_tipo)) {
            $this->nm_tipo = $nm_tipo;
        }
        if (is_string($descricao)) {
            $this->descricao = $descricao;
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
        if (is_numeric($this->ref_cod_biblioteca) && is_numeric($this->ref_usuario_cad) && is_string($this->nm_tipo)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_biblioteca)) {
                $campos .= "{$gruda}ref_cod_biblioteca";
                $valores .= "{$gruda}'{$this->ref_cod_biblioteca}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_cad)) {
                $campos .= "{$gruda}ref_usuario_cad";
                $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_string($this->nm_tipo)) {
                $nm_tipo = $db->escapeString($this->nm_tipo);
                $campos .= "{$gruda}nm_tipo";
                $valores .= "{$gruda}'{$nm_tipo}'";
                $gruda = ', ';
            }
            if (is_string($this->descricao)) {
                $descricao = $db->escapeString($this->descricao);
                $campos .= "{$gruda}descricao";
                $valores .= "{$gruda}'{$descricao}'";
                $gruda = ', ';
            }
            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';
            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId("{$this->_tabela}_cod_exemplar_tipo_seq");
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
        if (is_numeric($this->cod_exemplar_tipo) && is_numeric($this->ref_usuario_exc)) {
            $db = new clsBanco();
            $set = '';

            if (is_numeric($this->ref_cod_biblioteca)) {
                $set .= "{$gruda}ref_cod_biblioteca = '{$this->ref_cod_biblioteca}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_exc)) {
                $set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_cad)) {
                $set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_string($this->nm_tipo)) {
                $nm_tipo = $db->escapeString($this->nm_tipo);
                $set .= "{$gruda}nm_tipo = '{$nm_tipo}'";
                $gruda = ', ';
            }
            if (is_string($this->descricao)) {
                $descricao = $db->escapeString($this->descricao);
                $set .= "{$gruda}descricao = '{$descricao}'";
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
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_exemplar_tipo = '{$this->cod_exemplar_tipo}'");

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
    public function lista($int_cod_exemplar_tipo = null, $int_ref_cod_biblioteca = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $str_nm_tipo = null, $str_descricao = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $int_ref_cod_instituicao = null, $int_ref_cod_escola = null)
    {
        $db = new clsBanco();

        $sql = "SELECT {$this->_campos_lista}, b.ref_cod_instituicao, b.ref_cod_escola FROM {$this->_tabela} et, {$this->_schema}biblioteca b";

        $whereAnd = ' AND ';
        $filtros = ' WHERE et.ref_cod_biblioteca = b.cod_biblioteca ';

        if (is_numeric($int_cod_exemplar_tipo)) {
            $filtros .= "{$whereAnd} et.cod_exemplar_tipo = '{$int_cod_exemplar_tipo}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_biblioteca)) {
            $filtros .= "{$whereAnd} et.ref_cod_biblioteca = '{$int_ref_cod_biblioteca}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_exc)) {
            $filtros .= "{$whereAnd} et.ref_usuario_exc = '{$int_ref_usuario_exc}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_cad)) {
            $filtros .= "{$whereAnd} et.ref_usuario_cad = '{$int_ref_usuario_cad}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_nm_tipo)) {
            $str_nome_tipo = $db->escapeString($str_nm_tipo);
            $filtros .= "{$whereAnd} et.nm_tipo LIKE '%{$str_nome_tipo}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_descricao)) {
            $str_desc = $db->escapeString($str_descricao);
            $filtros .= "{$whereAnd} et.descricao LIKE '%{$str_desc}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_ini)) {
            $filtros .= "{$whereAnd} et.data_cadastro >= '{$date_data_cadastro_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_fim)) {
            $filtros .= "{$whereAnd} et.data_cadastro <= '{$date_data_cadastro_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_ini)) {
            $filtros .= "{$whereAnd} et.data_exclusao >= '{$date_data_exclusao_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_fim)) {
            $filtros .= "{$whereAnd} et.data_exclusao <= '{$date_data_exclusao_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_null($int_ativo) || $int_ativo) {
            $filtros .= "{$whereAnd} et.ativo = '1'";
            $whereAnd = ' AND ';
        } else {
            $filtros .= "{$whereAnd} et.ativo = '0'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_instituicao)) {
            $filtros .= "{$whereAnd} b.ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_escola)) {
            $filtros .= "{$whereAnd} b.ref_cod_escola = '{$int_ref_cod_escola}'";
            $whereAnd = ' AND ';
        }

        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} et, {$this->_schema}biblioteca b {$filtros}");

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
        if (is_numeric($this->cod_exemplar_tipo)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} et WHERE et.cod_exemplar_tipo = '{$this->cod_exemplar_tipo}'");
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
        if (is_numeric($this->cod_exemplar_tipo)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_exemplar_tipo = '{$this->cod_exemplar_tipo}'");
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
        if (is_numeric($this->cod_exemplar_tipo) && is_numeric($this->ref_usuario_exc)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }
}
