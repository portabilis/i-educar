<?php

use iEducar\Legacy\Model;

require_once 'include/pmieducar/geral.inc.php';
require_once 'lib/Portabilis/Utils/Database.php';

class clsPmieducarEscolaCurso extends Model
{
    public $ref_cod_escola;
    public $ref_cod_curso;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $autorizacao;
    public $anos_letivos;

    public function __construct($ref_cod_escola = null, $ref_cod_curso = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $data_cadastro = null, $data_exclusao = null, $ativo = null, $autorizacao = null, $anos_letivos = [])
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}escola_curso";

        $this->_campos_lista = $this->_todos_campos = 'ec.ref_cod_escola, ec.ref_cod_curso, ec.ref_usuario_exc, ec.ref_usuario_cad, ec.data_cadastro, ec.data_exclusao, ec.ativo, ec.autorizacao, ARRAY_TO_JSON(ec.anos_letivos) AS anos_letivos ';

        if (is_numeric($ref_usuario_exc)) {
                    $this->ref_usuario_exc = $ref_usuario_exc;
        }
        if (is_numeric($ref_usuario_cad)) {
                    $this->ref_usuario_cad = $ref_usuario_cad;
        }
        if (is_numeric($ref_cod_curso)) {
                    $this->ref_cod_curso = $ref_cod_curso;
        }
        if (is_numeric($ref_cod_escola)) {
                    $this->ref_cod_escola = $ref_cod_escola;
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
        if (is_string($autorizacao)) {
            $this->autorizacao = $autorizacao;
        }
        if (is_array($anos_letivos)) {
            $this->anos_letivos = $anos_letivos;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_curso) && is_numeric($this->ref_usuario_cad)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_escola)) {
                $campos .= "{$gruda}ref_cod_escola";
                $valores .= "{$gruda}'{$this->ref_cod_escola}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_curso)) {
                $campos .= "{$gruda}ref_cod_curso";
                $valores .= "{$gruda}'{$this->ref_cod_curso}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_cad)) {
                $campos .= "{$gruda}ref_usuario_cad";
                $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_string($this->autorizacao)) {
                $campos .= "{$gruda}autorizacao";
                $valores .= "{$gruda}'{$this->autorizacao}'";
                $grupo = ', ';
            }
            if (is_array($this->anos_letivos)) {
                $campos .= "{$gruda}anos_letivos";
                $valores .= "{$gruda} " . Portabilis_Utils_Database::arrayToPgArray($this->anos_letivos) . ' ';
                $grupo = ', ';
            }

            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';
            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return true;
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
        if (is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_curso) && is_numeric($this->ref_usuario_exc)) {
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

            if (is_string($this->autorizacao)) {
                $set .= "{$gruda}autorizacao = '{$this->autorizacao}'";
                $gruda = ', ';
            }

            if (is_array($this->anos_letivos)) {
                $set .= "{$gruda} anos_letivos = " . Portabilis_Utils_Database::arrayToPgArray($this->anos_letivos) . ' ';
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_escola = '{$this->ref_cod_escola}' AND ref_cod_curso = '{$this->ref_cod_curso}'");

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
    public function lista($int_ref_cod_escola = null, $int_ref_cod_curso = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $busca_nome_curso = false, $int_ref_cod_instituicao = null, $bool_curso_ativo = null)
    {
        $sql = "SELECT {$this->_campos_lista}, c.* FROM {$this->_tabela} ec, {$this->_schema}curso c";

        $whereAnd = ' AND ';
        $filtros = ' WHERE ec.ref_cod_curso = c.cod_curso ';

        if (is_numeric($int_ref_cod_escola)) {
            $filtros .= "{$whereAnd} ec.ref_cod_escola = '{$int_ref_cod_escola}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_curso)) {
            $filtros .= "{$whereAnd} ec.ref_cod_curso = '{$int_ref_cod_curso}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_exc)) {
            $filtros .= "{$whereAnd} ec.ref_usuario_exc = '{$int_ref_usuario_exc}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_cad)) {
            $filtros .= "{$whereAnd} ec.ref_usuario_cad = '{$int_ref_usuario_cad}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_ini)) {
            $filtros .= "{$whereAnd} ec.data_cadastro >= '{$date_data_cadastro_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_fim)) {
            $filtros .= "{$whereAnd} ec.data_cadastro <= '{$date_data_cadastro_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_ini)) {
            $filtros .= "{$whereAnd} ec.data_exclusao >= '{$date_data_exclusao_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_fim)) {
            $filtros .= "{$whereAnd} ec.data_exclusao <= '{$date_data_exclusao_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_null($int_ativo) || $int_ativo) {
            $filtros .= "{$whereAnd} ec.ativo = '1'";
            $whereAnd = ' AND ';
        } else {
            $filtros .= "{$whereAnd} ec.ativo = '0'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_instituicao)) {
            $filtros .= "{$whereAnd} c.ref_cod_instituicao = {$int_ref_cod_instituicao}";
            $whereAnd = ' AND ';
        }
        if (is_bool($bool_curso_ativo)) {
            if ($bool_curso_ativo) {
                $filtros .= "{$whereAnd} c.ativo = 1";
                $whereAnd = ' AND ';
            } else {
                $filtros .= "{$whereAnd} c.ativo = 0";
                $whereAnd = ' AND ';
            }
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} ec, {$this->_schema}curso c {$filtros}");

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
        if (is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_curso)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} ec WHERE ec.ref_cod_escola = '{$this->ref_cod_escola}' AND ec.ref_cod_curso = '{$this->ref_cod_curso}'");
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
        if (is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_curso)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_cod_escola = '{$this->ref_cod_escola}' AND ref_cod_curso = '{$this->ref_cod_curso}'");
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
        if (is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_curso) && is_numeric($this->ref_usuario_exc)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }

    /**
     * Exclui todos os registros referentes a um tipo de avaliacao
     */
    public function excluirTodos()
    {
        if (is_numeric($this->ref_cod_escola)) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->_tabela} WHERE ref_cod_escola = '{$this->ref_cod_escola}'");

            return true;
        }

        return false;
    }
}
