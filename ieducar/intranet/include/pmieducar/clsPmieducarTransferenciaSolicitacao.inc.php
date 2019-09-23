<?php

use iEducar\Legacy\Model;

require_once 'include/pmieducar/geral.inc.php';

class clsPmieducarTransferenciaSolicitacao extends Model
{
    public $cod_transferencia_solicitacao;
    public $ref_cod_transferencia_tipo;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_cod_matricula_entrada;
    public $ref_cod_matricula_saida;
    public $observacao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $data_transferencia;
    public $ref_cod_escola_destino;
    public $escola_destino_externa;
    public $estado_escola_destino_externa;
    public $municipio_escola_destino_externa;

    public function __construct($cod_transferencia_solicitacao = null, $ref_cod_transferencia_tipo = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $ref_cod_matricula_entrada = null, $ref_cod_matricula_saida = null, $observacao = null, $data_cadastro = null, $data_exclusao = null, $ativo = null, $data_transferencia = null, $escola_destino_externa = null, $ref_cod_escola_destino = null, $estado_escola_destino_externa = null, $municipio_escola_destino_externa = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}transferencia_solicitacao";

        $this->_campos_lista = $this->_todos_campos = 'ts.cod_transferencia_solicitacao, ts.ref_cod_transferencia_tipo, ts.ref_usuario_exc, ts.ref_usuario_cad, ts.ref_cod_matricula_entrada, ts.ref_cod_matricula_saida, ts.observacao, ts.data_cadastro, ts.data_exclusao, ts.ativo, ts.data_transferencia, ts.escola_destino_externa, ts.ref_cod_escola_destino, ts.estado_escola_destino_externa, ts.municipio_escola_destino_externa';

        if (is_numeric($ref_cod_transferencia_tipo)) {
                    $this->ref_cod_transferencia_tipo = $ref_cod_transferencia_tipo;
        }
        if (is_numeric($ref_usuario_exc)) {
                    $this->ref_usuario_exc = $ref_usuario_exc;
        }
        if (is_numeric($ref_usuario_cad)) {
                    $this->ref_usuario_cad = $ref_usuario_cad;
        }
        if (is_numeric($ref_cod_matricula_entrada)) {
                    $this->ref_cod_matricula_entrada = $ref_cod_matricula_entrada;
        }
        if (is_numeric($ref_cod_matricula_saida)) {
                    $this->ref_cod_matricula_saida = $ref_cod_matricula_saida;
        }

        if (is_numeric($cod_transferencia_solicitacao)) {
            $this->cod_transferencia_solicitacao = $cod_transferencia_solicitacao;
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
        if (is_string($data_transferencia)) {
            $this->data_transferencia = $data_transferencia;
        }
        if (is_string($escola_destino_externa)) {
            $this->escola_destino_externa = $escola_destino_externa;
        }
        if (is_numeric($ref_cod_escola_destino)) {
            $this->ref_cod_escola_destino = $ref_cod_escola_destino;
        }
        if (is_string($estado_escola_destino_externa)) {
            $this->estado_escola_destino_externa = $estado_escola_destino_externa;
        }
        if (is_string($municipio_escola_destino_externa)) {
            $this->municipio_escola_destino_externa = $municipio_escola_destino_externa;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_transferencia_tipo) && is_numeric($this->ref_usuario_cad) && is_numeric($this->ref_cod_matricula_saida)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_transferencia_tipo)) {
                $campos .= "{$gruda}ref_cod_transferencia_tipo";
                $valores .= "{$gruda}'{$this->ref_cod_transferencia_tipo}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_cad)) {
                $campos .= "{$gruda}ref_usuario_cad";
                $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_matricula_entrada)) {
                $campos .= "{$gruda}ref_cod_matricula_entrada";
                $valores .= "{$gruda}'{$this->ref_cod_matricula_entrada}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_matricula_saida)) {
                $campos .= "{$gruda}ref_cod_matricula_saida";
                $valores .= "{$gruda}'{$this->ref_cod_matricula_saida}'";
                $gruda = ', ';
            }
            if (is_string($this->observacao)) {
                $campos .= "{$gruda}observacao";
                $valores .= "{$gruda}'{$this->observacao}'";
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
            if (is_string($this->data_transferencia)) {
                $campos .= "{$gruda}data_transferencia";
                $valores .= "{$gruda}'{$this->data_transferencia}'";
                $gruda = ', ';
            }
            if (is_string($this->escola_destino_externa)) {
                $campos .= "{$gruda}escola_destino_externa";
                $valores .= "{$gruda}'{$this->escola_destino_externa}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_escola_destino)) {
                $campos .= "{$gruda}ref_cod_escola_destino";
                $valores .= "{$gruda}'{$this->ref_cod_escola_destino}'";
                $gruda = ', ';
            }
            if (is_string($this->estado_escola_destino_externa)) {
                $campos .= "{$gruda}estado_escola_destino_externa";
                $valores .= "{$gruda}'{$this->estado_escola_destino_externa}'";
                $gruda = ', ';
            }
            if (is_string($this->municipio_escola_destino_externa)) {
                $campos .= "{$gruda}municipio_escola_destino_externa";
                $valores .= "{$gruda}'{$this->municipio_escola_destino_externa}'";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId("{$this->_tabela}_cod_transferencia_solicitacao_seq");
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
        if (is_numeric($this->cod_transferencia_solicitacao) && is_numeric($this->ref_usuario_exc)) {
            $db = new clsBanco();
            $set = '';

            if (is_numeric($this->ref_cod_transferencia_tipo)) {
                $set .= "{$gruda}ref_cod_transferencia_tipo = '{$this->ref_cod_transferencia_tipo}'";
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
            if (is_numeric($this->ref_cod_matricula_entrada)) {
                $set .= "{$gruda}ref_cod_matricula_entrada = '{$this->ref_cod_matricula_entrada}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_matricula_saida)) {
                $set .= "{$gruda}ref_cod_matricula_saida = '{$this->ref_cod_matricula_saida}'";
                $gruda = ', ';
            }
            if (is_string($this->observacao)) {
                $set .= "{$gruda}observacao = '{$this->observacao}'";
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
            if (is_string($this->data_transferencia)) {
                $set .= "{$gruda}data_transferencia = '{$this->data_transferencia}'";
                $gruda = ', ';
            }
            if (is_string($this->escola_destino_externa)) {
                $set .= "{$gruda}escola_destino_externa = '{$this->escola_destino_externa}'";
                $gruda = ', ';
            }
            if (is_string($this->ref_cod_escola_destino)) {
                $set .= "{$gruda}ref_cod_escola_destino = '{$this->ref_cod_escola_destino}'";
                $gruda = ', ';
            }
            if (is_string($this->estado_escola_destino_externa)) {
                $set .= "{$gruda}estado_escola_destino_externa = '{$this->estado_escola_destino_externa}'";
                $gruda = ', ';
            }
            if (is_string($this->municipio_escola_destino_externa)) {
                $set .= "{$gruda}municipio_escola_destino_externa = '{$this->municipio_escola_destino_externa}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_transferencia_solicitacao = '{$this->cod_transferencia_solicitacao}'");

                return true;
            }
        }

        return false;
    }

    /**
     *
     * Apaga os registros de uma matricula em uma escola que foi a aceitação da transferencia
     *
     * @return boolean
     */
    public function desativaEntradaTransferencia()
    {
        if (is_numeric($this->cod_transferencia_solicitacao)) {
            $db = new clsBanco();
            $db->Consulta("UPDATE {$this->_tabela} SET ref_cod_matricula_entrada = NULL, data_transferencia = NULL,ref_usuario_exc = NULL, data_exclusao = NULL WHERE cod_transferencia_solicitacao = '{$this->cod_transferencia_solicitacao}'");

            return true;
        }

        return false;
    }

    /**
     * Retorna uma lista filtrados de acordo com os parametros
     *
     * @return array
     */
    public function lista($int_cod_transferencia_solicitacao = null, $int_ref_cod_transferencia_tipo = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $int_ref_cod_matricula_entrada = null, $int_ref_cod_matricula_saida = null, $str_observacao = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $date_data_transferencia_ini = null, $date_data_transferencia_fim = null, $int_ref_cod_aluno = null, $entrada_aluno = false, $int_ref_cod_escola = null, $int_ref_cod_serie = null, $mes = null, $transferido = null, $bool_matricula_entrada = null, $parar = false)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela} ts, {$this->_schema}matricula m";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (!is_null($bool_matricula_entrada)) {
            if ($bool_matricula_entrada == true) {
                $filtros .= "{$whereAnd}ts.ref_cod_matricula_entrada IS NOT NULL ";
                $whereAnd = ' AND ';
            } else {
                $filtros .= "{$whereAnd}ts.ref_cod_matricula_entrada IS NULL ";
                $whereAnd = ' AND ';
            }
        }
        if ($entrada_aluno == true) {
            $filtros .= "{$whereAnd}ts.ref_cod_matricula_entrada = m.cod_matricula";
            $whereAnd = ' AND ';
        } else { //if ($entrada_aluno == false)
            $filtros .= "{$whereAnd}ts.ref_cod_matricula_saida = m.cod_matricula";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_cod_transferencia_solicitacao)) {
            $filtros .= "{$whereAnd} ts.cod_transferencia_solicitacao = '{$int_cod_transferencia_solicitacao}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_transferencia_tipo)) {
            $filtros .= "{$whereAnd} ts.ref_cod_transferencia_tipo = '{$int_ref_cod_transferencia_tipo}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_exc)) {
            $filtros .= "{$whereAnd} ts.ref_usuario_exc = '{$int_ref_usuario_exc}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_cad)) {
            $filtros .= "{$whereAnd} ts.ref_usuario_cad = '{$int_ref_usuario_cad}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_matricula_entrada)) {
            $filtros .= "{$whereAnd} ts.ref_cod_matricula_entrada = '{$int_ref_cod_matricula_entrada}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_matricula_saida)) {
            $filtros .= "{$whereAnd} ts.ref_cod_matricula_saida = '{$int_ref_cod_matricula_saida}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_observacao)) {
            $filtros .= "{$whereAnd} ts.observacao LIKE '%{$str_observacao}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_ini)) {
            $filtros .= "{$whereAnd} ts.data_cadastro >= '{$date_data_cadastro_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_fim)) {
            $filtros .= "{$whereAnd} ts.data_cadastro <= '{$date_data_cadastro_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_ini)) {
            $filtros .= "{$whereAnd} ts.data_exclusao >= '{$date_data_exclusao_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_fim)) {
            $filtros .= "{$whereAnd} ts.data_exclusao <= '{$date_data_exclusao_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_transferencia_ini)) {
            $filtros .= "{$whereAnd} ts.data_transferencia >= '{$date_data_transferencia_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_transferencia_fim)) {
            $filtros .= "{$whereAnd} ts.data_transferencia <= '{$date_data_transferencia_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_aluno)) {
            $filtros .= "{$whereAnd} m.ref_cod_aluno = '{$int_ref_cod_aluno}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_escola)) {
            $filtros .= "{$whereAnd} m.ref_ref_cod_escola = '{$int_ref_cod_escola}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_serie)) {
            $filtros .= "{$whereAnd} m.ref_ref_cod_serie = '{$int_ref_cod_serie}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ativo)) {
            $filtros .= "{$whereAnd} ts.ativo = '{$int_ativo}'";
            $whereAnd = ' AND ';
        }
        if ($mes) {
            $mes = (int) $mes;
            $filtros .= "{$whereAnd} ( to_char(m.data_cadastro,'MM')::int = '$mes'
                                            OR to_char(m.data_exclusao,'MM')::int = '$mes' )";
            $whereAnd = ' AND ';
        }
        if (is_bool($transferido)) {
            if ($transferido == true) {
                $filtros .= "{$whereAnd} ts.data_transferencia IS NOT NULL";
                $whereAnd = ' AND ';
            } elseif ($transferido == false) {
                $filtros .= "{$whereAnd} ts.data_transferencia IS NULL";
                $whereAnd = ' AND ';
            }
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();
        if ($parar) {
            die($sql);
        }
        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} ts, {$this->_schema}matricula m {$filtros}");

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
        if (is_numeric($this->cod_transferencia_solicitacao)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} ts WHERE ts.cod_transferencia_solicitacao = '{$this->cod_transferencia_solicitacao}'");
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
        if (is_numeric($this->cod_transferencia_solicitacao)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_transferencia_solicitacao = '{$this->cod_transferencia_solicitacao}'");
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
        if (is_numeric($this->cod_transferencia_solicitacao) && is_numeric($this->ref_usuario_exc)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }

    /**
     * Retorna se existe solicitações para matrícula.
     *
     * @return boolean
     */
    public function existSolicitacaoTransferenciaAtiva()
    {
        if (is_numeric($this->ref_cod_matricula_saida)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_cod_matricula_saida = '{$this->ref_cod_matricula_saida}' AND ativo = '1'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }
}
