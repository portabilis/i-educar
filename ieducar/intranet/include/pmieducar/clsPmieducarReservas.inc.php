<?php

use iEducar\Legacy\Model;

class clsPmieducarReservas extends Model
{
    public $cod_reserva;
    public $ref_usuario_libera;
    public $ref_usuario_cad;
    public $ref_cod_cliente;
    public $data_reserva;
    public $data_prevista_disponivel;
    public $data_retirada;
    public $ref_cod_exemplar;
    public $ativo;

    public function __construct($cod_reserva = null, $ref_usuario_libera = null, $ref_usuario_cad = null, $ref_cod_cliente = null, $data_reserva = null, $data_prevista_disponivel = null, $data_retirada = null, $ref_cod_exemplar = null, $ativo = null)
    {
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}reservas";

        $this->_campos_lista = $this->_todos_campos = 'r.cod_reserva, r.ref_usuario_libera, r.ref_usuario_cad, r.ref_cod_cliente, r.data_reserva, r.data_prevista_disponivel, r.data_retirada, r.ref_cod_exemplar, r.ativo';

        if (is_numeric($ref_cod_exemplar)) {
            $this->ref_cod_exemplar = $ref_cod_exemplar;
        }
        if (is_numeric($ref_usuario_cad)) {
            $this->ref_usuario_cad = $ref_usuario_cad;
        }
        if (is_numeric($ref_usuario_libera)) {
            $this->ref_usuario_libera = $ref_usuario_libera;
        }
        if (is_numeric($ref_cod_cliente)) {
            $this->ref_cod_cliente = $ref_cod_cliente;
        }

        if (is_numeric($cod_reserva)) {
            $this->cod_reserva = $cod_reserva;
        }
        if (is_string($data_reserva)) {
            $this->data_reserva = $data_reserva;
        }
        if (is_string($data_prevista_disponivel)) {
            $this->data_prevista_disponivel = $data_prevista_disponivel;
        }
        if (is_string($data_retirada)) {
            $this->data_retirada = $data_retirada;
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
        if (is_numeric($this->ref_usuario_cad) && is_numeric($this->ref_cod_cliente) && is_numeric($this->ref_cod_exemplar)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_usuario_libera)) {
                $campos .= "{$gruda}ref_usuario_libera";
                $valores .= "{$gruda}'{$this->ref_usuario_libera}'";
                $gruda = ', ';
            }
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

            $campos .= "{$gruda}data_reserva";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';

            if (is_string($this->data_prevista_disponivel)) {
                $campos .= "{$gruda}data_prevista_disponivel";
                $valores .= "{$gruda}'{$this->data_prevista_disponivel}'";
                $gruda = ', ';
            }
            if (is_string($this->data_retirada)) {
                $campos .= "{$gruda}data_retirada";
                $valores .= "{$gruda}'{$this->data_retirada}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_exemplar)) {
                $campos .= "{$gruda}ref_cod_exemplar";
                $valores .= "{$gruda}'{$this->ref_cod_exemplar}'";
                $gruda = ', ';
            }
            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");
            $this->cod_reserva = $db->InsertId("{$this->_tabela}_cod_reserva_seq");
            if ($this->cod_reserva) {
                $detalhe = $this->detalhe();
            }

            return $this->cod_reserva;
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
        if (is_numeric($this->cod_reserva)) {
            $db = new clsBanco();
            $gruda = '';
            $set = '';

            if (is_numeric($this->ref_usuario_libera)) {
                $set .= "{$gruda}ref_usuario_libera = '{$this->ref_usuario_libera}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_cad)) {
                $set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_cliente)) {
                $set .= "{$gruda}ref_cod_cliente = '{$this->ref_cod_cliente}'";
                $gruda = ', ';
            }
            if (is_string($this->data_reserva)) {
                $set .= "{$gruda}data_reserva = '{$this->data_reserva}'";
                $gruda = ', ';
            }
            if (is_string($this->data_prevista_disponivel)) {
                $set .= "{$gruda}data_prevista_disponivel = '{$this->data_prevista_disponivel}'";
                $gruda = ', ';
            }
            if (is_string($this->data_retirada)) {
                $set .= "{$gruda}data_retirada = '{$this->data_retirada}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_exemplar)) {
                $set .= "{$gruda}ref_cod_exemplar = '{$this->ref_cod_exemplar}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ativo)) {
                $set .= "{$gruda}ativo = '{$this->ativo}'";
                $gruda = ', ';
            }

            if ($set) {
                $detalheAntigo = $this->detalhe();
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_reserva = '{$this->cod_reserva}'");

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
    public function lista($int_cod_reserva = null, $int_ref_usuario_libera = null, $int_ref_usuario_cad = null, $int_ref_cod_cliente = null, $date_data_reserva_ini = null, $date_data_reserva_fim = null, $date_data_prevista_disponivel_ini = null, $date_data_prevista_disponivel_fim = null, $date_data_retirada_ini = null, $date_data_retirada_fim = null, $int_ref_cod_exemplar = null, $int_ativo = null, $int_ref_cod_biblioteca = null, $int_ref_cod_instituicao = null, $int_ref_cod_escola = null, $data_retirada_null = null)
    {
        $sql = "SELECT {$this->_campos_lista}, a.ref_cod_biblioteca, b.ref_cod_instituicao, b.ref_cod_escola FROM {$this->_tabela} r, {$this->_schema}exemplar e, {$this->_schema}acervo a, {$this->_schema}biblioteca b";

        $whereAnd = ' AND ';
        $filtros = ' WHERE r.ref_cod_exemplar = e.cod_exemplar AND e.ref_cod_acervo = a.cod_acervo AND a.ref_cod_biblioteca = b.cod_biblioteca ';

        if (is_numeric($int_cod_reserva)) {
            $filtros .= "{$whereAnd} r.cod_reserva = '{$int_cod_reserva}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_libera)) {
            $filtros .= "{$whereAnd} r.ref_usuario_libera = '{$int_ref_usuario_libera}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_cad)) {
            $filtros .= "{$whereAnd} r.ref_usuario_cad = '{$int_ref_usuario_cad}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_cliente)) {
            $filtros .= "{$whereAnd} r.ref_cod_cliente = '{$int_ref_cod_cliente}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_reserva_ini)) {
            $filtros .= "{$whereAnd} r.data_reserva >= '{$date_data_reserva_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_reserva_fim)) {
            $filtros .= "{$whereAnd} r.data_reserva <= '{$date_data_reserva_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_prevista_disponivel_ini)) {
            $filtros .= "{$whereAnd} r.data_prevista_disponivel >= '{$date_data_prevista_disponivel_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_prevista_disponivel_fim)) {
            $filtros .= "{$whereAnd} r.data_prevista_disponivel <= '{$date_data_prevista_disponivel_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_retirada_ini)) {
            $filtros .= "{$whereAnd} r.data_retirada >= '{$date_data_retirada_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_retirada_fim)) {
            $filtros .= "{$whereAnd} r.data_retirada <= '{$date_data_retirada_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_exemplar)) {
            $filtros .= "{$whereAnd} r.ref_cod_exemplar = '{$int_ref_cod_exemplar}'";
            $whereAnd = ' AND ';
        }
        if (is_null($int_ativo) || $int_ativo) {
            $filtros .= "{$whereAnd} r.ativo = '1'";
            $whereAnd = ' AND ';
        } else {
            $filtros .= "{$whereAnd} r.ativo = '0'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_biblioteca)) {
            $filtros .= "{$whereAnd} a.ref_cod_biblioteca = '{$int_ref_cod_biblioteca}'";
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
        if (!is_null($data_retirada_null)) {
            if ($data_retirada_null) {
                $filtros .= "{$whereAnd} r.data_retirada is null";
                $whereAnd = ' AND ';
            } else {
                $filtros .= "{$whereAnd} r.data_retirada is not null";
                $whereAnd = ' AND ';
            }
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} r, {$this->_schema}exemplar e, {$this->_schema}acervo a, {$this->_schema}biblioteca b {$filtros}");

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
        if (is_numeric($this->cod_reserva)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} r WHERE r.cod_reserva = '{$this->cod_reserva}'");
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
        if (is_numeric($this->cod_reserva)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_reserva = '{$this->cod_reserva}'");
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
        if (is_numeric($this->cod_reserva)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }

    /**
     * Retorna uma lista com as ultimas reservas de cada exemplar
     *
     * @return string
     */
    public function getUltimasReservas($int_ref_cod_acervo, $int_limite = null)
    {
        if (is_numeric($int_ref_cod_acervo)) {
            $db = new clsBanco();
            $sql = "SELECT r.ref_cod_exemplar, max(data_prevista_disponivel) AS data_prevista_disponivel
                    FROM {$this->_tabela} r,
                         {$this->_schema}exemplar e
                    WHERE r.ref_cod_exemplar = e.cod_exemplar AND
                          e.ref_cod_acervo = '{$int_ref_cod_acervo}'
                    GROUP BY r.ref_cod_exemplar
                    ORDER BY max(data_prevista_disponivel) ASC";
            if ($int_limite) {
                $sql .= " limit '{$int_limite}'";
            }

            $db->Consulta($sql);
            $resultado = [];
            while ($db->ProximoRegistro()) {
                $resultado[] = $db->Tupla();
            }
            if (count($resultado)) {
                return $resultado;
            }

            return false;
        }

        return false;
    }

    /**
     * Retorna a ultima reserva do exemplar
     *
     * @return string
     */
    public function getUltimaReserva($int_ref_cod_exemplar)
    {
        if (is_numeric($int_ref_cod_exemplar)) {
            $db = new clsBanco();
            $sql = "SELECT r.ref_cod_exemplar, max(data_prevista_disponivel) AS data_prevista_disponivel
                    FROM {$this->_tabela} r,
                         {$this->_schema}exemplar e
                    WHERE r.ref_cod_exemplar = e.cod_exemplar AND
                          e.cod_exemplar = '{$int_ref_cod_exemplar}'
                    GROUP BY r.ref_cod_exemplar
                    ORDER BY max(data_prevista_disponivel) ASC";
            $db->Consulta($sql);
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }
}
