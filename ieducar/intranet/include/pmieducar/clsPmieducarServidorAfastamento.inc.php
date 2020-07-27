<?php

use iEducar\Legacy\Model;

require_once 'include/pmieducar/geral.inc.php';

class clsPmieducarServidorAfastamento extends Model
{
    public $ref_cod_servidor;
    public $sequencial;
    public $ref_cod_motivo_afastamento;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $data_cadastro;
    public $data_exclusao;
    public $data_retorno;
    public $data_saida;
    public $ativo;
    public $ref_cod_instituicao;

    public function __construct($ref_cod_servidor = null, $sequencial = null, $ref_cod_motivo_afastamento = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $data_cadastro = null, $data_exclusao = null, $data_retorno = null, $data_saida = null, $ativo = null, $ref_cod_instituicao = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}servidor_afastamento";

        $this->_campos_lista = $this->_todos_campos = 'ref_cod_servidor, sequencial, ref_cod_motivo_afastamento, ref_usuario_exc, ref_usuario_cad, data_cadastro, data_exclusao, data_retorno, data_saida, ativo, id';

        if (is_numeric($ref_cod_motivo_afastamento)) {
                    $this->ref_cod_motivo_afastamento = $ref_cod_motivo_afastamento;
        }
        if (is_numeric($ref_usuario_exc)) {
                    $this->ref_usuario_exc = $ref_usuario_exc;
        }
        if (is_numeric($ref_usuario_cad)) {
                    $this->ref_usuario_cad = $ref_usuario_cad;
        }
        if (is_numeric($ref_cod_servidor)) {
                    $this->ref_cod_servidor = $ref_cod_servidor;
        }

        if (is_numeric($sequencial)) {
            $this->sequencial = $sequencial;
        }
        if (is_string($data_cadastro)) {
            $this->data_cadastro = $data_cadastro;
        }
        if (is_string($data_exclusao)) {
            $this->data_exclusao = $data_exclusao;
        }
        if (is_string($data_retorno)) {
            $this->data_retorno = $data_retorno;
        }
        if (is_string($data_saida)) {
            $this->data_saida = $data_saida;
        }
        if (is_numeric($ativo)) {
            $this->ativo = $ativo;
        }
        if (is_numeric($ref_cod_instituicao)) {
                    $this->ref_cod_instituicao = $ref_cod_instituicao;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_servidor) && is_numeric($this->ref_cod_motivo_afastamento) && is_numeric($this->ref_usuario_cad) && is_string($this->data_saida) && ($this->ref_cod_instituicao)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_servidor)) {
                $campos .= "{$gruda}ref_cod_servidor";
                $valores .= "{$gruda}'{$this->ref_cod_servidor}'";
                $gruda = ', ';
            }
            $this->sequencial = $db->CampoUnico("SELECT COALESCE( MAX( sa.sequencial ), 0 ) + 1 AS sequencial
                                                    FROM pmieducar.servidor_afastamento sa
                                                   WHERE sa.ref_cod_servidor        = {$this->ref_cod_servidor}
                                                     AND sa.ref_ref_cod_instituicao = {$this->ref_cod_instituicao}");
            $campos .= "{$gruda}sequencial";
            $valores .= "{$gruda}'{$this->sequencial}'";
            $gruda = ', ';
            if (is_numeric($this->ref_cod_motivo_afastamento)) {
                $campos .= "{$gruda}ref_cod_motivo_afastamento";
                $valores .= "{$gruda}'{$this->ref_cod_motivo_afastamento}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_cad)) {
                $campos .= "{$gruda}ref_usuario_cad";
                $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';
            if ($this->data_retorno != '') {
                if (is_string($this->data_retorno)) {
                    $campos .= "{$gruda}data_retorno";
                    $valores .= "{$gruda}'{$this->data_retorno}'";
                    $gruda = ', ';
                }
            }
            if (is_string($this->data_saida)) {
                $campos .= "{$gruda}data_saida";
                $valores .= "{$gruda}'{$this->data_saida}'";
                $gruda = ', ';
            }
            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';
            if (is_numeric($this->ref_cod_instituicao)) {
                $campos .= "{$gruda}ref_ref_cod_instituicao";
                $valores .= "{$gruda}'{$this->ref_cod_instituicao}'";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId('pmieducar.servidor_afastamento_id_seq');
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
        if (is_numeric($this->ref_cod_servidor) && is_numeric($this->sequencial) && is_numeric($this->ref_usuario_exc) && is_numeric($this->ref_cod_instituicao)) {
            $db = new clsBanco();
            $set = '';

            if (is_numeric($this->ref_cod_motivo_afastamento)) {
                $set .= "{$gruda}ref_cod_motivo_afastamento = '{$this->ref_cod_motivo_afastamento}'";
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
            if (is_string($this->data_cadastro)) {
                $set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
                $gruda = ', ';
            }
            $set .= "{$gruda}data_exclusao = NOW()";
            $gruda = ', ';
            if (is_string($this->data_retorno) && $this->data_retorno != '') {
                $set .= "{$gruda}data_retorno = '{$this->data_retorno}'";
                $gruda = ', ';
            }
            if (is_string($this->data_saida)) {
                $set .= "{$gruda}data_saida = '{$this->data_saida}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ativo)) {
                $set .= "{$gruda}ativo = '{$this->ativo}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_servidor = '{$this->ref_cod_servidor}' AND sequencial = '{$this->sequencial}' AND ref_ref_cod_instituicao = '{$this->ref_cod_instituicao}'");

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
    public function lista($int_ref_cod_servidor = null, $int_sequencial = null, $int_ref_cod_motivo_afastamento = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $date_data_retorno_ini = null, $date_data_retorno_fim = null, $date_data_saida_ini = null, $date_data_saida_fim = null, $int_ativo = null, $int_ref_cod_instituicao = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_ref_cod_servidor)) {
            $filtros .= "{$whereAnd} ref_cod_servidor = '{$int_ref_cod_servidor}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_sequencial)) {
            $filtros .= "{$whereAnd} sequencial = '{$int_sequencial}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_motivo_afastamento)) {
            $filtros .= "{$whereAnd} ref_cod_motivo_afastamento = '{$int_ref_cod_motivo_afastamento}'";
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
        if (is_string($date_data_retorno_ini)) {
            $filtros .= "{$whereAnd} data_retorno >= '{$date_data_retorno_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_retorno_fim)) {
            $filtros .= "{$whereAnd} data_retorno <= '{$date_data_retorno_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_saida_ini)) {
            $filtros .= "{$whereAnd} data_saida >= '{$date_data_saida_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_saida_fim)) {
            $filtros .= "{$whereAnd} data_saida <= '{$date_data_saida_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_null($int_ativo) || $int_ativo) {
            $filtros .= "{$whereAnd} ativo = '1'";
            $whereAnd = ' AND ';
        } else {
            $filtros .= "{$whereAnd} ativo = '0'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_instituicao)) {
            $filtros .= "{$whereAnd} ref_ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
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
        if (is_numeric($this->ref_cod_servidor) && is_numeric($this->sequencial) && is_numeric($this->ref_cod_instituicao)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_servidor = '{$this->ref_cod_servidor}' AND sequencial = '{$this->sequencial}' AND ref_ref_cod_instituicao = '{$this->ref_cod_instituicao}'");
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
        if (is_numeric($this->ref_cod_servidor) && is_numeric($this->sequencial) && is_numeric($this->ref_cod_instituicao)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_cod_servidor = '{$this->ref_cod_servidor}' AND sequencial = '{$this->sequencial}' AND ref_ref_cod_instituicao = '{$this->ref_cod_instituicao}'");
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
        if (is_numeric($this->ref_cod_servidor) && is_numeric($this->sequencial) && is_numeric($this->ref_usuario_exc) && is_numeric($this->ref_cod_instituicao)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }

    /**
     * Retorna um character dizendo se o servidor está afastado (campo sequencial) ou não (0)
     *
     * @return array
     */
    public function afastado($int_ref_cod_servidor = null, $int_ref_cod_instituicao = null)
    {
        if (is_numeric($int_ref_cod_servidor) && is_numeric($int_ref_cod_instituicao)) {
            $db = new clsBanco();

            return $db->CampoUnico("SELECT CASE WHEN MAX( sa.sequencial ) > 0 THEN MAX( sa.sequencial )
                                            ELSE 0
                                            END
                                       FROM pmieducar.servidor_afastamento sa
                                      WHERE sa.ref_cod_servidor        = {$int_ref_cod_servidor}
                                        AND sa.ref_ref_cod_instituicao = {$int_ref_cod_instituicao}
                                        AND (sa.data_retorno            IS NULL
                                           OR sa.data_retorno          > NOW() )
                                        AND sa.ativo                   = 1");
        }

        return false;
    }
}
