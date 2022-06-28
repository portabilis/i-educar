<?php

use iEducar\Legacy\Model;

class clsPmieducarMatriculaOcorrenciaDisciplinar extends Model
{
    public $ref_cod_matricula;
    public $ref_cod_tipo_ocorrencia_disciplinar;
    public $sequencial;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $observacao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $cod_ocorrencia_disciplinar;

    public function __construct($ref_cod_matricula = null, $ref_cod_tipo_ocorrencia_disciplinar = null, $sequencial = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $observacao = null, $data_cadastro = null, $data_exclusao = null, $ativo = null, $visivel_pais = null, $cod_ocorrencia_disciplinar = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}matricula_ocorrencia_disciplinar";

        $this->_campos_lista = $this->_todos_campos = 'ref_cod_matricula, ref_cod_tipo_ocorrencia_disciplinar, sequencial, ref_usuario_exc, ref_usuario_cad, observacao, data_cadastro, data_exclusao, ativo, visivel_pais, cod_ocorrencia_disciplinar';

        if (is_numeric($ref_usuario_exc)) {
            $this->ref_usuario_exc = $ref_usuario_exc;
        }
        if (is_numeric($ref_usuario_cad)) {
            $this->ref_usuario_cad = $ref_usuario_cad;
        }
        if (is_numeric($ref_cod_tipo_ocorrencia_disciplinar)) {
            $this->ref_cod_tipo_ocorrencia_disciplinar = $ref_cod_tipo_ocorrencia_disciplinar;
        }
        if (is_numeric($ref_cod_matricula)) {
            $this->ref_cod_matricula = $ref_cod_matricula;
        }

        if (is_numeric($sequencial)) {
            $this->sequencial = $sequencial;
        }
        if (is_string($observacao)) {
            $this->observacao = $observacao;
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

        $this->visivel_pais = $visivel_pais;
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_tipo_ocorrencia_disciplinar) && is_numeric($this->ref_usuario_cad) && is_string($this->observacao)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_matricula)) {
                $campos .= "{$gruda}ref_cod_matricula";
                $valores .= "{$gruda}'{$this->ref_cod_matricula}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_tipo_ocorrencia_disciplinar)) {
                $campos .= "{$gruda}ref_cod_tipo_ocorrencia_disciplinar";
                $valores .= "{$gruda}'{$this->ref_cod_tipo_ocorrencia_disciplinar}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_cad)) {
                $campos .= "{$gruda}ref_usuario_cad";
                $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_string($this->observacao)) {
                $observacao = $db->escapeString($this->observacao);
                $campos .= "{$gruda}observacao";
                $valores .= "{$gruda}'{$observacao}'";
                $gruda = ', ';
            }
            if (is_string($this->data_cadastro)) {
                $campos .= "{$gruda}data_cadastro";
                $valores .= "{$gruda}'{$this->data_cadastro}'";
                $gruda = ', ';
            } else {
                $campos .= "{$gruda}data_cadastro";
                $valores .= "{$gruda}NOW()";
                $gruda = ', ';
            }

            if (is_numeric($this->visivel_pais)) {
                $campos .= "{$gruda}visivel_pais";
                $valores .= "{$gruda}'{$this->visivel_pais}'";
                $gruda = ', ';
            }

            $sequencial = $this->getSequencialAluno($this->ref_cod_matricula, $this->ref_cod_tipo_ocorrencia_disciplinar);

            $campos .= "{$gruda}sequencial";
            $valores .= "{$gruda}'{$sequencial}'";
            $gruda = ', ';

            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId('pmieducar.ocorrencia_disciplinar_seq');
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
        if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_tipo_ocorrencia_disciplinar) && is_numeric($this->sequencial) && is_numeric($this->ref_usuario_exc)) {
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
            if (is_string($this->observacao)) {
                $observacao = $db->escapeString($this->observacao);
                $set .= "{$gruda}observacao = '{$observacao}'";
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

            if (is_numeric($this->visivel_pais)) {
                $set .= "{$gruda}visivel_pais = '{$this->visivel_pais}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_tipo_ocorrencia_disciplinar)) {
                $set .= "{$gruda}ref_cod_tipo_ocorrencia_disciplinar = '{$this->ref_cod_tipo_ocorrencia_disciplinar}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_matricula = '{$this->ref_cod_matricula}' AND ref_cod_tipo_ocorrencia_disciplinar = '{$this->ref_cod_tipo_ocorrencia_disciplinar}' AND sequencial = '{$this->sequencial}'");

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
    public function lista($int_ref_cod_matricula = null, $int_ref_cod_tipo_ocorrencia_disciplinar = null, $int_sequencial = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $str_observacao = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $visivel_pais = null, $cod_ocorrencia_disciplinar = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_ref_cod_matricula)) {
            $filtros .= "{$whereAnd} ref_cod_matricula = '{$int_ref_cod_matricula}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_tipo_ocorrencia_disciplinar)) {
            $filtros .= "{$whereAnd} ref_cod_tipo_ocorrencia_disciplinar = '{$int_ref_cod_tipo_ocorrencia_disciplinar}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_sequencial)) {
            $filtros .= "{$whereAnd} sequencial = '{$int_sequencial}'";
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
        if (is_string($str_observacao)) {
            $filtros .= "{$whereAnd} observacao LIKE '%{$str_observacao}%'";
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

        if (is_numeric($visivel_pais)) {
            $filtros .= "{$whereAnd} visivel_pais = '{$visivel_pais}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($cod_ocorrencia_disciplinar)) {
            $filtros .= "{$whereAnd} cod_ocorrencia_disciplinar = '{$cod_ocorrencia_disciplinar}'";
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
        if (is_numeric($this->cod_ocorrencia_disciplinar)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_ocorrencia_disciplinar = '{$this->cod_ocorrencia_disciplinar}' AND ativo=1");
            $db->ProximoRegistro();

            return $db->Tupla();
        } elseif (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_tipo_ocorrencia_disciplinar) && is_numeric($this->sequencial)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_matricula = '{$this->ref_cod_matricula}' AND ref_cod_tipo_ocorrencia_disciplinar = '{$this->ref_cod_tipo_ocorrencia_disciplinar}' AND sequencial = '{$this->sequencial}' AND ativo=1");
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
        if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_tipo_ocorrencia_disciplinar) && is_numeric($this->sequencial)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_cod_matricula = '{$this->ref_cod_matricula}' AND ref_cod_tipo_ocorrencia_disciplinar = '{$this->ref_cod_tipo_ocorrencia_disciplinar}' AND sequencial = '{$this->sequencial}'");
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
        if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_tipo_ocorrencia_disciplinar) && is_numeric($this->sequencial) && is_numeric($this->ref_usuario_exc)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }

    public function getSequencialAluno($int_ref_cod_matricula, $int_ref_cod_tipo_ocorrencia_disciplinar)
    {
        if (is_numeric($int_ref_cod_matricula) && is_numeric($int_ref_cod_tipo_ocorrencia_disciplinar)) {
            $db = new clsBanco();

            $consulta = "SELECT COALESCE(MAX(sequencial),0) + 1 FROM {$this->_tabela} WHERE ref_cod_matricula = '{$int_ref_cod_matricula}' AND ref_cod_tipo_ocorrencia_disciplinar = '{$int_ref_cod_tipo_ocorrencia_disciplinar}'";

            return $db->CampoUnico($consulta);
        }

        return false;
    }
}
