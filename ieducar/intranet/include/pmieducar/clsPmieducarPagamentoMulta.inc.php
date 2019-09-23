<?php

use iEducar\Legacy\Model;

require_once 'include/pmieducar/geral.inc.php';

class clsPmieducarPagamentoMulta extends Model
{
    public $cod_pagamento_multa;
    public $ref_usuario_cad;
    public $ref_cod_cliente;
    public $valor_pago;
    public $data_cadastro;
    public $ref_cod_biblioteca;

    public function __construct($cod_pagamento_multa = null, $ref_usuario_cad = null, $ref_cod_cliente = null, $valor_pago = null, $data_cadastro = null, $ref_cod_biblioteca = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}pagamento_multa";

        $this->_campos_lista = $this->_todos_campos = 'cod_pagamento_multa, ref_usuario_cad, ref_cod_cliente, valor_pago, data_cadastro, ref_cod_biblioteca';

        if (is_numeric($ref_usuario_cad)) {
                    $this->ref_usuario_cad = $ref_usuario_cad;
        }
        if (is_numeric($ref_cod_cliente)) {
                    $this->ref_cod_cliente = $ref_cod_cliente;
        }

        if (is_numeric($cod_pagamento_multa)) {
            $this->cod_pagamento_multa = $cod_pagamento_multa;
        }
        if (is_numeric($valor_pago)) {
            $this->valor_pago = $valor_pago;
        }
        if (is_string($data_cadastro)) {
            $this->data_cadastro = $data_cadastro;
        }
        if (is_numeric($ref_cod_biblioteca)) {
                    $this->ref_cod_biblioteca = $ref_cod_biblioteca;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_usuario_cad) && is_numeric($this->ref_cod_cliente) && is_numeric($this->valor_pago) && is_numeric($this->ref_cod_biblioteca)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_usuario_cad)) {
                $campos .= "{$gruda}ref_usuario_cad";
                $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_cliente)) {
                $campos .= "{$gruda}ref_cod_cliente";
                $valores .= "{$gruda}'{$this->ref_cod_cliente}'";
                $gruda = ', ';
            }
            if (is_numeric($this->valor_pago)) {
                $campos .= "{$gruda}valor_pago";
                $valores .= "{$gruda}'{$this->valor_pago}'";
                $gruda = ', ';
            }
            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';
            if (is_numeric($this->ref_cod_biblioteca)) {
                $campos .= "{$gruda}ref_cod_biblioteca";
                $valores .= "{$gruda}'{$this->ref_cod_biblioteca}'";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId("{$this->_tabela}_cod_pagamento_multa_seq");
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
        if (is_numeric($this->cod_pagamento_multa)) {
            $db = new clsBanco();
            $set = '';

            if (is_numeric($this->ref_usuario_cad)) {
                $set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_cliente)) {
                $set .= "{$gruda}ref_cod_cliente = '{$this->ref_cod_cliente}'";
                $gruda = ', ';
            }
            if (is_numeric($this->valor_pago)) {
                $set .= "{$gruda}valor_pago = '{$this->valor_pago}'";
                $gruda = ', ';
            }
            if (is_string($this->data_cadastro)) {
                $set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_biblioteca)) {
                $set .= "{$gruda}ref_cod_biblioteca = '{$this->ref_cod_biblioteca}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_pagamento_multa = '{$this->cod_pagamento_multa}'");

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
    public function lista($int_cod_pagamento_multa = null, $int_ref_usuario_cad = null, $int_ref_cod_cliente = null, $int_valor_pago = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $int_ref_cod_biblioteca = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_cod_pagamento_multa)) {
            $filtros .= "{$whereAnd} cod_pagamento_multa = '{$int_cod_pagamento_multa}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_cad)) {
            $filtros .= "{$whereAnd} ref_usuario_cad = '{$int_ref_usuario_cad}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_cliente)) {
            $filtros .= "{$whereAnd} ref_cod_cliente = '{$int_ref_cod_cliente}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_valor_pago)) {
            $filtros .= "{$whereAnd} valor_pago = '{$int_valor_pago}'";
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
        if (is_numeric($int_ref_cod_biblioteca)) {
            $filtros .= "{$whereAnd} ref_cod_biblioteca = '{$int_ref_cod_biblioteca}'";
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
        if (is_numeric($this->cod_pagamento_multa)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_pagamento_multa = '{$this->cod_pagamento_multa}'");
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
        if (is_numeric($this->cod_pagamento_multa)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_pagamento_multa = '{$this->cod_pagamento_multa}'");
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
        if (is_numeric($this->cod_pagamento_multa)) {
        }

        return false;
    }

    public function totalPago()
    {
        if (is_numeric($this->ref_cod_cliente) && is_numeric($this->ref_cod_biblioteca)) {
            $db = new clsBanco();
            $total_pago = $db->CampoUnico("SELECT SUM( valor_pago ) FROM {$this->_tabela} WHERE ref_cod_cliente = '{$this->ref_cod_cliente}' AND ref_cod_biblioteca = '{$this->ref_cod_biblioteca}'");
            if (!is_numeric($total_pago)) {
                $total_pago = 0;
            }

            return $total_pago;
        }

        return false;
    }
}
