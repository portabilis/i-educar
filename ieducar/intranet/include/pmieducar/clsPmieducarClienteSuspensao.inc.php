<?php

use iEducar\Legacy\Model;

class clsPmieducarClienteSuspensao extends Model
{
    public $sequencial;
    public $ref_cod_cliente;
    public $ref_cod_motivo_suspensao;
    public $ref_usuario_libera;
    public $ref_usuario_suspende;
    public $dias;
    public $data_suspensao;
    public $data_liberacao;

    public function __construct($sequencial = null, $ref_cod_cliente = null, $ref_cod_motivo_suspensao = null, $ref_usuario_libera = null, $ref_usuario_suspende = null, $dias = null, $data_suspensao = null, $data_liberacao = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}cliente_suspensao";

        $this->_campos_lista = $this->_todos_campos = 'sequencial, ref_cod_cliente, ref_cod_motivo_suspensao, ref_usuario_libera, ref_usuario_suspende, dias, data_suspensao, data_liberacao';

        if (is_numeric($ref_usuario_suspende)) {
            $this->ref_usuario_suspende = $ref_usuario_suspende;
        }
        if (is_numeric($ref_usuario_libera)) {
            $this->ref_usuario_libera = $ref_usuario_libera;
        }
        if (is_numeric($ref_cod_motivo_suspensao)) {
            $this->ref_cod_motivo_suspensao = $ref_cod_motivo_suspensao;
        }
        if (is_numeric($ref_cod_cliente)) {
            $this->ref_cod_cliente = $ref_cod_cliente;
        }

        if (is_numeric($sequencial)) {
            $this->sequencial = $sequencial;
        }
        if (is_numeric($dias)) {
            $this->dias = $dias;
        }
        if (is_string($data_suspensao)) {
            $this->data_suspensao = $data_suspensao;
        }
        if (is_string($data_liberacao)) {
            $this->data_liberacao = $data_liberacao;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_cliente) && is_numeric($this->ref_cod_motivo_suspensao) && is_numeric($this->ref_usuario_suspende) && is_numeric($this->dias)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            $sequencial = $db->CampoUnico("SELECT COUNT(*)+1 FROM pmieducar.cliente_suspensao WHERE ref_cod_cliente = {$this->ref_cod_cliente}");
            if (is_numeric($sequencial)) {
                $campos .= "{$gruda}sequencial";
                $valores .= "{$gruda}'{$sequencial}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_cliente)) {
                $campos .= "{$gruda}ref_cod_cliente";
                $valores .= "{$gruda}'{$this->ref_cod_cliente}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_motivo_suspensao)) {
                $campos .= "{$gruda}ref_cod_motivo_suspensao";
                $valores .= "{$gruda}'{$this->ref_cod_motivo_suspensao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_libera)) {
                $campos .= "{$gruda}ref_usuario_libera";
                $valores .= "{$gruda}'{$this->ref_usuario_libera}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_suspende)) {
                $campos .= "{$gruda}ref_usuario_suspende";
                $valores .= "{$gruda}'{$this->ref_usuario_suspende}'";
                $gruda = ', ';
            }
            if (is_numeric($this->dias)) {
                $campos .= "{$gruda}dias";
                $valores .= "{$gruda}'{$this->dias}'";
                $gruda = ', ';
            }
            $campos .= "{$gruda}data_suspensao";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';
            if (is_string($this->data_liberacao)) {
                $campos .= "{$gruda}data_liberacao";
                $valores .= "{$gruda}'{$this->data_liberacao}'";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $sequencial;
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
        if (is_numeric($this->sequencial) && is_numeric($this->ref_cod_cliente)) {
            $db = new clsBanco();
            $set = '';

            if (is_numeric($this->ref_usuario_libera)) {
                $set .= "{$gruda}ref_usuario_libera = '{$this->ref_usuario_libera}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_suspende)) {
                $set .= "{$gruda}ref_usuario_suspende = '{$this->ref_usuario_suspende}'";
                $gruda = ', ';
            }
            if (is_numeric($this->dias)) {
                $set .= "{$gruda}dias = '{$this->dias}'";
                $gruda = ', ';
            }
            if (is_string($this->data_suspensao)) {
                $set .= "{$gruda}data_suspensao = '{$this->data_suspensao}'";
                $gruda = ', ';
            }
            $set .= "{$gruda}data_liberacao = NOW()";
            $gruda = ', ';

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE sequencial = '{$this->sequencial}' AND ref_cod_cliente = '{$this->ref_cod_cliente}'");

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
    public function lista($int_sequencial = null, $int_ref_cod_cliente = null, $int_ref_cod_motivo_suspensao = null, $int_ref_usuario_libera = null, $int_ref_usuario_suspende = null, $int_dias = null, $date_data_suspensao_ini = null, $date_data_suspensao_fim = null, $date_data_liberacao_ini = null, $date_data_liberacao_fim = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_sequencial)) {
            $filtros .= "{$whereAnd} sequencial = '{$int_sequencial}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_cliente)) {
            $filtros .= "{$whereAnd} ref_cod_cliente = '{$int_ref_cod_cliente}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_motivo_suspensao)) {
            $filtros .= "{$whereAnd} ref_cod_motivo_suspensao = '{$int_ref_cod_motivo_suspensao}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_libera)) {
            $filtros .= "{$whereAnd} ref_usuario_libera = '{$int_ref_usuario_libera}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_suspende)) {
            $filtros .= "{$whereAnd} ref_usuario_suspende = '{$int_ref_usuario_suspende}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_dias)) {
            $filtros .= "{$whereAnd} dias = '{$int_dias}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_suspensao_ini)) {
            $filtros .= "{$whereAnd} data_suspensao >= '{$date_data_suspensao_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_suspensao_fim)) {
            $filtros .= "{$whereAnd} data_suspensao <= '{$date_data_suspensao_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_liberacao_ini)) {
            $filtros .= "{$whereAnd} data_liberacao >= '{$date_data_liberacao_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_liberacao_fim)) {
            $filtros .= "{$whereAnd} data_liberacao <= '{$date_data_liberacao_fim}'";
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
        if (is_numeric($this->sequencial) && is_numeric($this->ref_cod_cliente) && is_numeric($this->ref_cod_motivo_suspensao)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE sequencial = '{$this->sequencial}' AND ref_cod_cliente = '{$this->ref_cod_cliente}' AND ref_cod_motivo_suspensao = '{$this->ref_cod_motivo_suspensao}'");
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
        if (is_numeric($this->sequencial) && is_numeric($this->ref_cod_cliente) && is_numeric($this->ref_cod_motivo_suspensao)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE sequencial = '{$this->sequencial}' AND ref_cod_cliente = '{$this->ref_cod_cliente}' AND ref_cod_motivo_suspensao = '{$this->ref_cod_motivo_suspensao}'");
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
        if (is_numeric($this->sequencial) && is_numeric($this->ref_cod_cliente) && is_numeric($this->ref_cod_motivo_suspensao)) {
        }

        return false;
    }
}
