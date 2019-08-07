<?php

use iEducar\Legacy\Model;

require_once 'include/pmieducar/geral.inc.php';

class clsModulesItinerarioTransporteEscolar extends Model
{
    public $cod_itinerario_transporte_escolar;
    public $ref_cod_rota_transporte_escolar;
    public $seq;
    public $ref_cod_ponto_transporte_escolar;
    public $ref_cod_veiculo;
    public $hora;
    public $tipo;

    public function __construct(
        $cod_itinerario_transporte_escolar = null,
        $ref_cod_rota_transporte_escolar = null
        ,
        $seq = null,
        $ref_cod_ponto_transporte_escolar = null,
        $ref_cod_veiculo = null,
        $hora = null,
        $tipo = null
    ) {
        $db = new clsBanco();
        $this->_schema = 'modules.';
        $this->_tabela = "{$this->_schema}itinerario_transporte_escolar";

        $this->_campos_lista = $this->_todos_campos = ' cod_itinerario_transporte_escolar, ref_cod_rota_transporte_escolar, ref_cod_ponto_transporte_escolar, ref_cod_veiculo, seq, hora, tipo';

        if (is_numeric($cod_itinerario_transporte_escolar)) {
            $this->cod_itinerario_transporte_escolar = $cod_itinerario_transporte_escolar;
        }

        if (is_numeric($ref_cod_rota_transporte_escolar)) {
            $this->ref_cod_rota_transporte_escolar = $ref_cod_rota_transporte_escolar;
        }

        if (is_numeric($seq)) {
            $this->seq = $seq;
        }

        if (is_numeric($ref_cod_ponto_transporte_escolar)) {
            $this->ref_cod_ponto_transporte_escolar = $ref_cod_ponto_transporte_escolar;
        }

        if (is_numeric($ref_cod_veiculo)) {
            $this->ref_cod_veiculo = $ref_cod_veiculo;
        }

        if (is_string($hora)) {
            $this->hora = $hora;
        }

        if (is_string($tipo)) {
            $this->tipo = $tipo;
        }
    }

    /**
     * Cria um novo registro.
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_rota_transporte_escolar)
            && is_numeric($this->seq) && is_numeric($this->ref_cod_ponto_transporte_escolar)
            && is_string($this->tipo)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_rota_transporte_escolar)) {
                $campos .= "{$gruda}ref_cod_rota_transporte_escolar";
                $valores .= "{$gruda}'{$this->ref_cod_rota_transporte_escolar}'";
                $gruda = ', ';
            }

            if (is_numeric($this->seq)) {
                $campos .= "{$gruda}seq";
                $valores .= "{$gruda}'{$this->seq}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_ponto_transporte_escolar)) {
                $campos .= "{$gruda}ref_cod_ponto_transporte_escolar";
                $valores .= "{$gruda}'{$this->ref_cod_ponto_transporte_escolar}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_veiculo)) {
                $campos .= "{$gruda}ref_cod_veiculo";
                $valores .= "{$gruda}'{$this->ref_cod_veiculo}'";
                $gruda = ', ';
            }

            if ($this->checktime($this->hora)) {
                $campos .= "{$gruda}hora";
                $valores .= "{$gruda}'{$this->hora}'";
                $gruda = ', ';
            }

            if (is_string($this->tipo)) {
                $campos .= "{$gruda}tipo";
                $valores .= "{$gruda}'{$this->tipo}'";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");
            $this->cod_itinerario_transporte_escolar = $db->InsertId("{$this->_tabela}_seq");

            return (int) $this->cod_itinerario_transporte_escolar;
        }

        return false;
    }

    /**
     * Edita os dados de um registro.
     *
     * @return bool
     */
    public function edita()
    {
        if (is_string($this->cod_itinerario_transporte_escolar)) {
            $db = new clsBanco();
            $set = '';
            $gruda = '';

            if (is_numeric($this->cod_itinerario_transporte_escolar)) {
                $set .= "{$gruda}cod_itinerario_transporte_escolar = '{$this->cod_itinerario_transporte_escolar}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_rota_transporte_escolar)) {
                $set .= "{$gruda}ref_cod_rota_transporte_escolar = '{$this->ref_cod_rota_transporte_escolar}'";
                $gruda = ', ';
            }

            if (is_numeric($this->seq)) {
                $set .= "{$gruda}seq = '{$this->seq}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_ponto_transporte_escolar)) {
                $set .= "{$gruda}ref_cod_ponto_transporte_escolar = '{$this->ref_cod_ponto_transporte_escolar}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_veiculo)) {
                $set .= "{$gruda}ref_cod_veiculo = '{$this->ref_cod_veiculo}'";
                $gruda = ', ';
            }

            if (is_string($this->hora)) {
                $set .= "{$gruda}hora = '{$this->hora}'";
                $gruda = ', ';
            }

            if (is_string($this->tipo)) {
                $set .= "{$gruda}tipo = '{$this->tipo}'";
                $gruda = ', ';
            }
            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_itinerario_transporte_escolar = '{$this->cod_itinerario_transporte_escolar}'");

                return true;
            }
        }

        return false;
    }

    /**
     * Retorna uma lista de registros filtrados de acordo com os parâmetros.
     *
     * @return array
     */
    public function lista(
        $cod_itinerario_transporte_escolar = null,
        $ref_cod_rota_transporte_escolar = null,
        $seq = null,
        $ref_cod_veiculo = null,
        $tipo = null,
        $ref_cod_ponto_transporte_escolar = null
    ) {
        $sql = "SELECT {$this->_campos_lista},
         (SELECT descricao
           FROM modules.ponto_transporte_escolar
           WHERE ref_cod_ponto_transporte_escolar = cod_ponto_transporte_escolar) as descricao,
         (SELECT descricao || ', Placa: ' || placa || ', Motorista: ' || pessoa.nome
           FROM modules.veiculo
           LEFT JOIN modules.motorista ON (motorista.cod_motorista = veiculo.ref_cod_motorista)
           LEFT JOIN cadastro.pessoa ON (pessoa.idpes = motorista.ref_idpes)
           WHERE ref_cod_veiculo = cod_veiculo) as nome_onibus FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($cod_itinerario_transporte_escolar)) {
            $filtros .= "{$whereAnd} cod_itinerario_transporte_escolar = '{$cod_itinerario_transporte_escolar}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($ref_cod_rota_transporte_escolar)) {
            $filtros .= "{$whereAnd} ref_cod_rota_transporte_escolar = '{$ref_cod_rota_transporte_escolar}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($seq)) {
            $filtros .= "{$whereAnd} seq = '{$seq}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($ref_cod_veiculo)) {
            $filtros .= "{$whereAnd} ref_cod_veiculo = '{$ref_cod_veiculo}'";
            $whereAnd = ' AND ';
        }

        if (is_string($tipo)) {
            $filtros .= "{$whereAnd} tipo = '{$tipo}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($ref_cod_ponto_transporte_escolar)) {
            $filtros .= "{$whereAnd} ref_cod_ponto_transporte_escolar = '{$ref_cod_ponto_transporte_escolar}'";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista)) + 2;
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

    public function listaPontos($ref_cod_rota_transporte_escolar = null)
    {
        $sql = "SELECT ref_cod_ponto_transporte_escolar,
         (SELECT descricao
           FROM modules.ponto_transporte_escolar
           WHERE ref_cod_ponto_transporte_escolar = cod_ponto_transporte_escolar) as descricao,
           (SELECT tipo
           FROM modules.ponto_transporte_escolar
           WHERE ref_cod_ponto_transporte_escolar = cod_ponto_transporte_escolar) as tipo FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($ref_cod_rota_transporte_escolar)) {
            $filtros .= "{$whereAnd} ref_cod_rota_transporte_escolar = '{$ref_cod_rota_transporte_escolar}'";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista)) + 2;
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
     * Retorna um array com os dados de um registro.
     *
     * @return array
     */
    public function detalhe()
    {
        if (is_numeric($this->cod_itinerario_transporte_escolar)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos}
                       FROM {$this->_tabela}
                      WHERE cod_itinerario_transporte_escolar = '{$this->cod_itinerario_transporte_escolar}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Retorna um array com os dados de um registro.
     *
     * @return array
     */
    public function existe()
    {
        if (is_numeric($this->cod_rota_transporte_escolar)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_rota_transporte_escolar = '{$this->cod_rota_transporte_escolar}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Exclui um registro.
     *
     * @return bool
     */
    public function excluir()
    {
        if (is_numeric($this->cod_rota_transporte_escolar)) {
            $sql = "DELETE FROM {$this->_tabela} WHERE ref_cod_rota_transporte_escolar = '{$this->ref_cod_rota_transporte_escolar}'";
            $db = new clsBanco();
            $db->Consulta($sql);

            return true;
        }

        return false;
    }

    /**
     * Exclui todos registros.
     *
     * @return bool
     */
    public function excluirTodos($ref_cod_rota_transporte_escolar)
    {
        if (is_numeric($ref_cod_rota_transporte_escolar)) {
            $sql = "DELETE FROM {$this->_tabela} WHERE ref_cod_rota_transporte_escolar = '{$ref_cod_rota_transporte_escolar}'";
            $db = new clsBanco();
            $db->Consulta($sql);

            return true;
        }

        return false;
    }

    public function checktime($time)
    {
        list($hour, $minute) = explode(':', $time);

        if ($hour > -1 && $hour < 24 && $minute > -1 && $minute < 60 && is_numeric($hour) && is_numeric($minute)) {
            return true;
        } else {
            return false;
        }
    }
}
