<?php

use iEducar\Legacy\Model;

class clsPmieducarPreRequisito extends Model
{
    public $cod_pre_requisito;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $schema_;
    public $tabela;
    public $nome;
    public $sql;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function __construct($cod_pre_requisito = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $schema_ = null, $tabela = null, $nome = null, $sql = null, $data_cadastro = null, $data_exclusao = null, $ativo = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}pre_requisito";

        $this->_campos_lista = $this->_todos_campos = 'cod_pre_requisito, ref_usuario_exc, ref_usuario_cad, schema_, tabela, nome, sql, data_cadastro, data_exclusao, ativo';

        if (is_numeric($ref_usuario_exc)) {
            $this->ref_usuario_exc = $ref_usuario_exc;
        }
        if (is_numeric($ref_usuario_cad)) {
            $this->ref_usuario_cad = $ref_usuario_cad;
        }

        if (is_numeric($cod_pre_requisito)) {
            $this->cod_pre_requisito = $cod_pre_requisito;
        }
        if (is_string($schema_)) {
            $this->schema_ = $schema_;
        }
        if (is_string($tabela)) {
            $this->tabela = $tabela;
        }
        if (is_string($nome)) {
            $this->nome = $nome;
        }
        if (is_string($sql)) {
            $this->sql = $sql;
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
        if (is_numeric($this->ref_usuario_cad) && is_string($this->schema_) && is_string($this->tabela) && is_string($this->nome)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_usuario_cad)) {
                $campos .= "{$gruda}ref_usuario_cad";
                $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_string($this->schema_)) {
                $campos .= "{$gruda}schema_";
                $valores .= "{$gruda}'{$this->schema_}'";
                $gruda = ', ';
            }
            if (is_string($this->tabela)) {
                $campos .= "{$gruda}tabela";
                $valores .= "{$gruda}'{$this->tabela}'";
                $gruda = ', ';
            }
            if (is_string($this->nome)) {
                $campos .= "{$gruda}nome";
                $valores .= "{$gruda}'{$this->nome}'";
                $gruda = ', ';
            }
            if (is_string($this->sql) && $this->sql) {
                $campos .= "{$gruda}sql";
                $valores .= "{$gruda}'{$this->sql}'";
                $gruda = ', ';
            }
            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';
            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId("{$this->_tabela}_cod_pre_requisito_seq");
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
        if (is_numeric($this->cod_pre_requisito) && is_numeric($this->ref_usuario_exc)) {
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
            if (is_string($this->schema_)) {
                $set .= "{$gruda}schema_ = '{$this->schema_}'";
                $gruda = ', ';
            }
            if (is_string($this->tabela)) {
                $set .= "{$gruda}tabela = '{$this->tabela}'";
                $gruda = ', ';
            }
            if (is_string($this->nome)) {
                $set .= "{$gruda}nome = '{$this->nome}'";
                $gruda = ', ';
            }
            if (is_string($this->sql) && $this->sql) {
                $set .= "{$gruda}sql = '{$this->sql}'";
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
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_pre_requisito = '{$this->cod_pre_requisito}'");

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
    public function lista($int_cod_pre_requisito = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $str_schema_ = null, $str_tabela = null, $str_nome = null, $str_sql = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_cod_pre_requisito)) {
            $filtros .= "{$whereAnd} cod_pre_requisito = '{$int_cod_pre_requisito}'";
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
        if (is_string($str_schema_)) {
            $filtros .= "{$whereAnd} schema_ LIKE '%{$str_schema_}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_tabela)) {
            $filtros .= "{$whereAnd} tabela LIKE '%{$str_tabela}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_nome)) {
            $filtros .= "{$whereAnd} nome LIKE '%{$str_nome}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_sql)) {
            $filtros .= "{$whereAnd} sql LIKE '%{$str_sql}%'";
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
        if (is_null($int_ativo) || $int_ativo) {
            $filtros .= "{$whereAnd} ativo = '1'";
            $whereAnd = ' AND ';
        } else {
            $filtros .= "{$whereAnd} ativo = '0'";
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
        if (is_numeric($this->cod_pre_requisito)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_pre_requisito = '{$this->cod_pre_requisito}'");
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
        if (is_numeric($this->cod_pre_requisito)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_pre_requisito = '{$this->cod_pre_requisito}'");
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
        if (is_numeric($this->cod_pre_requisito) && is_numeric($this->ref_usuario_exc)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }
}
