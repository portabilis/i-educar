<?php

use iEducar\Legacy\Model;

class clsPmieducarFaltaAtraso extends Model
{
    public $cod_falta_atraso;
    public $ref_cod_escola;
    public $ref_ref_cod_instituicao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_cod_servidor;
    public $tipo;
    public $data_falta_atraso;
    public $qtd_horas;
    public $qtd_min;
    public $justificada;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_servidor_funcao;

    public function __construct(
        $cod_falta_atraso = null,
        $ref_cod_escola = null,
        $ref_ref_cod_instituicao = null,
        $ref_usuario_exc = null,
        $ref_usuario_cad = null,
        $ref_cod_servidor = null,
        $tipo = null,
        $data_falta_atraso = null,
        $qtd_horas = null,
        $qtd_min = null,
        $justificada = null,
        $data_cadastro = null,
        $data_exclusao = null,
        $ativo = null,
        $ref_cod_servidor_funcao = null
    ) {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = $this->_schema . 'falta_atraso';

        $this->_campos_lista = $this->_todos_campos = 'cod_falta_atraso, ref_cod_escola, falta_atraso.ref_ref_cod_instituicao, ref_usuario_exc, ref_usuario_cad, falta_atraso.ref_cod_servidor, tipo, data_falta_atraso, qtd_horas, qtd_min, justificada, data_cadastro, data_exclusao, ativo, ref_cod_servidor_funcao';

        if (is_numeric($ref_cod_escola)) {
            $this->ref_cod_escola = $ref_cod_escola;
        }

        if (is_numeric($ref_usuario_cad)) {
            $this->ref_usuario_cad = $ref_usuario_cad;
        }

        if (is_numeric($ref_usuario_exc)) {
            $this->ref_usuario_exc = $ref_usuario_exc;
        }

        if (is_numeric($ref_cod_servidor) && is_numeric($ref_ref_cod_instituicao)) {
            $this->ref_cod_servidor = $ref_cod_servidor;
            $this->ref_ref_cod_instituicao = $ref_ref_cod_instituicao;
        }

        if (is_numeric($cod_falta_atraso)) {
            $this->cod_falta_atraso = $cod_falta_atraso;
        }

        if (is_numeric($tipo)) {
            $this->tipo = $tipo;
        }

        if (is_string($data_falta_atraso)) {
            $this->data_falta_atraso = $data_falta_atraso;
        }

        if (is_numeric($qtd_horas)) {
            $this->qtd_horas = $qtd_horas;
        }

        if (is_numeric($qtd_min)) {
            $this->qtd_min = $qtd_min;
        }

        if (is_numeric($justificada)) {
            $this->justificada = $justificada;
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

        if (is_numeric($ref_cod_servidor_funcao)) {
            $this->ref_cod_servidor_funcao = $ref_cod_servidor_funcao;
        }
    }

    /**
     * Cria um novo registro.
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_escola) &&
            is_numeric($this->ref_ref_cod_instituicao) && is_numeric($this->ref_usuario_cad) &&
            is_numeric($this->ref_cod_servidor) && is_numeric($this->tipo) &&
            is_string($this->data_falta_atraso) && is_numeric($this->justificada)
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

            if (is_numeric($this->ref_usuario_cad)) {
                $campos .= "{$gruda}ref_usuario_cad";
                $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_servidor)) {
                $campos .= "{$gruda}ref_cod_servidor";
                $valores .= "{$gruda}'{$this->ref_cod_servidor}'";
                $gruda = ', ';
            }

            if (is_numeric($this->tipo)) {
                $campos .= "{$gruda}tipo";
                $valores .= "{$gruda}'{$this->tipo}'";
                $gruda = ', ';
            }

            if (is_string($this->data_falta_atraso)) {
                $campos .= "{$gruda}data_falta_atraso";
                $valores .= "{$gruda}'{$this->data_falta_atraso}'";
                $gruda = ', ';
            }

            if (is_numeric($this->qtd_horas)) {
                $campos .= "{$gruda}qtd_horas";
                $valores .= "{$gruda}'{$this->qtd_horas}'";
                $gruda = ', ';
            }

            if (is_numeric($this->qtd_min)) {
                $campos .= "{$gruda}qtd_min";
                $valores .= "{$gruda}'{$this->qtd_min}'";
                $gruda = ', ';
            }

            if (is_numeric($this->justificada)) {
                $campos .= "{$gruda}justificada";
                $valores .= "{$gruda}'{$this->justificada}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_servidor_funcao)) {
                $campos .= "{$gruda}ref_cod_servidor_funcao";
                $valores .= "{$gruda}'{$this->ref_cod_servidor_funcao}'";
                $gruda = ', ';
            }

            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';

            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';

            $db->Consulta("INSERT INTO {$this->_tabela} ($campos) VALUES($valores)");

            return $db->InsertId("{$this->_tabela}_cod_falta_atraso_seq");
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
        if (is_numeric($this->cod_falta_atraso) && is_numeric($this->ref_usuario_exc)) {
            $db = new clsBanco();
            $gruda = '';
            $set = '';

            if (is_numeric($this->ref_cod_escola)) {
                $set .= "{$gruda}ref_cod_escola = '{$this->ref_cod_escola}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_ref_cod_instituicao)) {
                $set .= "{$gruda}ref_ref_cod_instituicao = '{$this->ref_ref_cod_instituicao}'";
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

            if (is_numeric($this->ref_cod_servidor)) {
                $set .= "{$gruda}ref_cod_servidor = '{$this->ref_cod_servidor}'";
                $gruda = ', ';
            }

            if (is_numeric($this->tipo)) {
                $set .= "{$gruda}tipo = '{$this->tipo}'";
                $gruda = ', ';
            }

            if (is_string($this->data_falta_atraso)) {
                $set .= "{$gruda}data_falta_atraso = '{$this->data_falta_atraso}'";
                $gruda = ', ';
            }

            if (is_numeric($this->qtd_horas)) {
                $set .= "{$gruda}qtd_horas = '{$this->qtd_horas}'";
                $gruda = ', ';
            }

            if (is_numeric($this->qtd_min)) {
                $set .= "{$gruda}qtd_min = '{$this->qtd_min}'";
                $gruda = ', ';
            }

            if (is_numeric($this->justificada)) {
                $set .= "{$gruda}justificada = '{$this->justificada}'";
                $gruda = ', ';
            }

            if (is_string($this->data_cadastro)) {
                $set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_servidor_funcao)) {
                $set .= "{$gruda}ref_cod_servidor_funcao = '{$this->ref_cod_servidor_funcao}'";
                $gruda = ', ';
            }

            $set .= "{$gruda}data_exclusao = NOW()";
            $gruda = ', ';

            if (is_numeric($this->ativo)) {
                $set .= "{$gruda}ativo = '{$this->ativo}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_falta_atraso = '{$this->cod_falta_atraso}'");

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
        $int_cod_falta_atraso = null,
        $int_ref_cod_escola = null,
        $int_ref_ref_cod_instituicao = null,
        $int_ref_usuario_exc = null,
        $int_ref_usuario_cad = null,
        $int_ref_cod_servidor = null,
        $int_tipo = null,
        $date_data_falta_atraso_ini = null,
        $date_data_falta_atraso_fim = null,
        $int_qtd_horas = null,
        $int_qtd_min = null,
        $int_justificada = null,
        $date_data_cadastro_ini = null,
        $date_data_cadastro_fim = null,
        $date_data_exclusao_ini = null,
        $date_data_exclusao_fim = null,
        $int_ativo = null
    ) {
        $sql = "
            SELECT {$this->_campos_lista}, matricula
            FROM {$this->_tabela}
            LEFT JOIN pmieducar.servidor_funcao ON servidor_funcao.cod_servidor_funcao = falta_atraso.ref_cod_servidor_funcao
        ";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_cod_falta_atraso)) {
            $filtros .= "{$whereAnd} cod_falta_atraso = '{$int_cod_falta_atraso}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_escola)) {
            $filtros .= "{$whereAnd} ref_cod_escola = '{$int_ref_cod_escola}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_ref_cod_instituicao)) {
            $filtros .= "{$whereAnd} falta_atraso.ref_ref_cod_instituicao = '{$int_ref_ref_cod_instituicao}'";
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

        if (is_numeric($int_ref_cod_servidor)) {
            $filtros .= "{$whereAnd} falta_atraso.ref_cod_servidor = '{$int_ref_cod_servidor}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_tipo)) {
            $filtros .= "{$whereAnd} tipo = '{$int_tipo}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_falta_atraso_ini)) {
            $filtros .= "{$whereAnd} data_falta_atraso >= '{$date_data_falta_atraso_ini}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_falta_atraso_fim)) {
            $filtros .= "{$whereAnd} data_falta_atraso <= '{$date_data_falta_atraso_fim}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_qtd_horas)) {
            $filtros .= "{$whereAnd} qtd_horas = '{$int_qtd_horas}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_qtd_min)) {
            $filtros .= "{$whereAnd} qtd_min = '{$int_qtd_min}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_justificada)) {
            $filtros .= "{$whereAnd} justificada = '{$int_justificada}'";
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
     * Retorna um array com os dados de um registro.
     *
     * @return array
     */
    public function detalhe()
    {
        if (is_numeric($this->cod_falta_atraso)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_falta_atraso = '{$this->cod_falta_atraso}'");
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
        if (is_numeric($this->cod_falta_atraso)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_falta_atraso = '{$this->cod_falta_atraso}'");
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
        if (is_numeric($this->cod_falta_atraso) && is_numeric($this->ref_usuario_exc)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }

    /**
     * Retorna uma lista de registros filtrados de acordo com os parâmetros.
     *
     * @return array
     */
    public function listaHorasEscola(
        $int_ref_cod_servidor = null,
        $int_ref_ref_cod_instituicao = null,
        $int_ref_cod_escola = null
    ) {
        $sql = '
          SELECT
            SUM(qtd_horas) AS horas,
            SUM(qtd_min) AS minutos,
            ref_cod_escola,
            ref_ref_cod_instituicao
          FROM
        ' . $this->_tabela;

        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_ref_cod_servidor)) {
            $filtros .= "{$whereAnd} ref_cod_servidor = '{$int_ref_cod_servidor}'";
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

        $filtros .= "{$whereAnd} justificada <> '0'";
        $whereAnd = ' AND ';

        $filtros .= "{$whereAnd} ativo <> '0'";
        $whereAnd = ' AND ';

        $groupBy = ' GROUP BY ref_cod_escola, ref_ref_cod_instituicao';

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM ({$sql}{$filtros}{$groupBy}) AS countsubquery");

        $sql .= $filtros . $groupBy . $this->getLimite();

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

    public function excluiTodosPorServidor($codServidor): void
    {
        $db = new clsBanco();
        $db->Consulta("DELETE FROM {$this->_tabela} WHERE ref_cod_servidor = '{$codServidor}'");
    }
}
