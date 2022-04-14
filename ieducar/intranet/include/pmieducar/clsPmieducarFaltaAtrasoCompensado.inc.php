<?php

use iEducar\Legacy\Model;

class clsPmieducarFaltaAtrasoCompensado extends Model
{
    public $cod_compensado;
    public $ref_cod_escola;
    public $ref_ref_cod_instituicao;
    public $ref_cod_servidor;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $data_inicio;
    public $data_fim;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function __construct(
        $cod_compensado = null,
        $ref_cod_escola = null,
        $ref_ref_cod_instituicao = null,
        $ref_cod_servidor = null,
        $ref_usuario_exc = null,
        $ref_usuario_cad = null,
        $data_inicio = null,
        $data_fim = null,
        $data_cadastro = null,
        $data_exclusao = null,
        $ativo = null
    ) {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = $this->_schema . 'falta_atraso_compensado';

        $this->_campos_lista = $this->_todos_campos = 'cod_compensado, ref_cod_escola, ref_ref_cod_instituicao, ref_cod_servidor, ref_usuario_exc, ref_usuario_cad, data_inicio, data_fim, data_cadastro, data_exclusao, ativo';

        if (is_numeric($ref_cod_escola)) {
            $this->ref_cod_escola = $ref_cod_escola;
        }

        if (is_numeric($ref_cod_servidor) && is_numeric($ref_ref_cod_instituicao)) {
            $this->ref_cod_servidor = $ref_cod_servidor;
            $this->ref_ref_cod_instituicao = $ref_ref_cod_instituicao;
        }

        if (is_numeric($ref_usuario_exc)) {
            $this->ref_usuario_exc = $ref_usuario_exc;
        }

        if (is_numeric($ref_usuario_cad)) {
            $this->ref_usuario_cad = $ref_usuario_cad;
        }

        if (is_numeric($cod_compensado)) {
            $this->cod_compensado = $cod_compensado;
        }

        if (is_string($data_inicio)) {
            $this->data_inicio = $data_inicio;
        }

        if (is_string($data_fim)) {
            $this->data_fim = $data_fim;
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
     * Cria um novo registro.
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_escola) && is_numeric($this->ref_ref_cod_instituicao) &&
            is_numeric($this->ref_cod_servidor) && is_numeric($this->ref_usuario_cad) &&
            is_string($this->data_inicio) && is_string($this->data_fim)
        ) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_escola)) {
                $campos .= "{$gruda}ref_cod_escola";
                $valores .= "{$gruda}'{$this->ref_cod_escola}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_ref_cod_instituicao)) {
                $campos .= "{$gruda}ref_ref_cod_instituicao";
                $valores .= "{$gruda}'{$this->ref_ref_cod_instituicao}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_servidor)) {
                $campos .= "{$gruda}ref_cod_servidor";
                $valores .= "{$gruda}'{$this->ref_cod_servidor}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_usuario_cad)) {
                $campos .= "{$gruda}ref_usuario_cad";
                $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }

            if (is_string($this->data_inicio)) {
                $campos .= "{$gruda}data_inicio";
                $valores .= "{$gruda}'{$this->data_inicio}'";
                $gruda = ', ';
            }

            if (is_string($this->data_fim)) {
                $campos .= "{$gruda}data_fim";
                $valores .= "{$gruda}'{$this->data_fim}'";
                $gruda = ', ';
            }

            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';

            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';

            $db->Consulta("INSERT INTO {$this->_tabela} ($campos) VALUES ($valores)");

            return $db->InsertId("{$this->_tabela}_cod_compensado_seq");
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
        if (is_numeric($this->cod_compensado) && is_numeric($this->ref_usuario_exc)) {
            $db = new clsBanco();
            $set = '';

            if (is_numeric($this->ref_cod_escola)) {
                $set .= "{$gruda}ref_cod_escola = '{$this->ref_cod_escola}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_ref_cod_instituicao)) {
                $set .= "{$gruda}ref_ref_cod_instituicao = '{$this->ref_ref_cod_instituicao}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_servidor)) {
                $set .= "{$gruda}ref_cod_servidor = '{$this->ref_cod_servidor}'";
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

            if (is_string($this->data_inicio)) {
                $set .= "{$gruda}data_inicio = '{$this->data_inicio}'";
                $gruda = ', ';
            }

            if (is_string($this->data_fim)) {
                $set .= "{$gruda}data_fim = '{$this->data_fim}'";
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
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_compensado = '{$this->cod_compensado}'");

                return true;
            }
        }

        return false;
    }

    /**
     * Retorna uma lista de registros filtrados de acordo com os parâmetros
     *
     * @return array
     */
    public function lista(
        $int_cod_compensado = null,
        $int_ref_cod_escola = null,
        $int_ref_ref_cod_instituicao = null,
        $int_ref_cod_servidor = null,
        $int_ref_usuario_exc = null,
        $int_ref_usuario_cad = null,
        $date_data_inicio_ini = null,
        $date_data_inicio_fim = null,
        $date_data_fim_ini = null,
        $date_data_fim_fim = null,
        $date_data_cadastro_ini = null,
        $date_data_cadastro_fim = null,
        $date_data_exclusao_ini = null,
        $date_data_exclusao_fim = null,
        $int_ativo = null
    ) {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_cod_compensado)) {
            $filtros .= "{$whereAnd} cod_compensado = '{$int_cod_compensado}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_escola)) {
            $filtros .= "{$whereAnd} ref_cod_escola = '{$int_ref_cod_escola}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_ref_cod_instituicao)) {
            $filtros .= "{$whereAnd} ref_ref_cod_instituicao = '{$int_ref_ref_cod_instituicao}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_servidor)) {
            $filtros .= "{$whereAnd} ref_cod_servidor = '{$int_ref_cod_servidor}'";
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

        if (is_string($date_data_inicio_ini)) {
            $filtros .= "{$whereAnd} data_inicio >= '{$date_data_inicio_ini}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_inicio_fim)) {
            $filtros .= "{$whereAnd} data_inicio <= '{$date_data_inicio_fim}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_fim_ini)) {
            $filtros .= "{$whereAnd} data_fim >= '{$date_data_fim_ini}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_fim_fim)) {
            $filtros .= "{$whereAnd} data_fim <= '{$date_data_fim_fim}'";
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

        if (is_NULL($int_ativo) || $int_ativo) {
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
     * Retorna um array com os dados de um registro.
     *
     * @return array
     */
    public function detalhe()
    {
        if (is_numeric($this->cod_compensado)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_compensado = '{$this->cod_compensado}'");
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
        if (is_numeric($this->cod_compensado)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_compensado = '{$this->cod_compensado}'");
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
        if (is_numeric($this->cod_compensado) && is_numeric($this->ref_usuario_exc)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }

    /**
     * Retorna a quantidade de horas compensadas.
     *
     * @return array
     */
    public function ServidorHorasCompensadas(
        $int_ref_cod_servidor,
        $int_ref_cod_escola,
        $int_ref_cod_instituicao
    ) {
        if (is_numeric($int_ref_cod_servidor)) {
            $db = new clsBanco();
            $db->Consulta("
        SELECT
          fac.data_inicio,
          fac.data_fim
        FROM
          pmieducar.falta_atraso_compensado fac
        WHERE
          fac.ref_cod_servidor            = '{$int_ref_cod_servidor}'
          AND fac.ref_cod_escola          = '{$int_ref_cod_escola}'
          AND fac.ref_ref_cod_instituicao = '{$int_ref_cod_instituicao}'");

            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();
                $resultado[] = $tupla;
            }

            $horas_total = 0;
            $minutos_total = 0;

            if ($resultado) {
                foreach ($resultado as $registro) {
                    $data_atual = strtotime($registro['data_inicio']);
                    $data_fim = strtotime($registro['data_fim']);

                    do {
                        $db2 = new clsBanco();

                        $dia_semana = $db2->CampoUnico('SELECT EXTRACT(DOW FROM (date \'' . date('Y-m-d', $data_atual) . '\') + 1)');
                        $obj_servidor = new clsPmieducarServidor();
                        $horas = $obj_servidor->qtdhoras(
                            $int_ref_cod_servidor,
                            $int_ref_cod_escola,
                            $int_ref_cod_instituicao,
                            $dia_semana
                        );

                        if ($horas) {
                            $horas_total += $horas['hora'];
                            $minutos_total += $horas['min'];
                        }

                        $data_atual += 86400;
                    } while ($data_atual <= $data_fim);
                }
            }

            $res['hora'] = $horas_total;
            $res['min'] = $minutos_total;

            return $res;
        }

        return false;
    }
}
