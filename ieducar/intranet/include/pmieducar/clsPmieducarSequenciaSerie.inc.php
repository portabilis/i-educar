<?php

use iEducar\Legacy\Model;

class clsPmieducarSequenciaSerie extends Model
{
    public $ref_serie_origem;
    public $ref_serie_destino;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function __construct($ref_serie_origem = null, $ref_serie_destino = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $data_cadastro = null, $data_exclusao = null, $ativo = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}sequencia_serie";

        $this->_campos_lista = $this->_todos_campos = 'ss.ref_serie_origem, ss.ref_serie_destino, ss.ref_usuario_exc, ss.ref_usuario_cad, ss.data_cadastro, ss.data_exclusao, ss.ativo';

        if (is_numeric($ref_serie_destino)) {
            $this->ref_serie_destino = $ref_serie_destino;
        }
        if (is_numeric($ref_serie_origem)) {
            $this->ref_serie_origem = $ref_serie_origem;
        }
        if (is_numeric($ref_usuario_exc)) {
            $this->ref_usuario_exc = $ref_usuario_exc;
        }
        if (is_numeric($ref_usuario_cad)) {
            $this->ref_usuario_cad = $ref_usuario_cad;
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
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_serie_origem) && is_numeric($this->ref_serie_destino) && is_numeric($this->ref_usuario_cad)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_serie_origem)) {
                $campos .= "{$gruda}ref_serie_origem";
                $valores .= "{$gruda}'{$this->ref_serie_origem}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_serie_destino)) {
                $campos .= "{$gruda}ref_serie_destino";
                $valores .= "{$gruda}'{$this->ref_serie_destino}'";
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
        if (is_numeric($this->ref_serie_origem) && is_numeric($this->ref_serie_destino) && is_numeric($this->ref_usuario_exc)) {
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
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_serie_origem = '{$this->ref_serie_origem}' AND ref_serie_destino = '{$this->ref_serie_destino}'");

                return true;
            }
        }

        return false;
    }

    /**
     * Edita os dados de um registro
     *
     * @return bool
     */
    public function editar($serie_origem_old, $serie_destino_old)
    {
        if (is_numeric($this->ref_serie_origem) && is_numeric($this->ref_serie_destino) && is_numeric($this->ref_usuario_exc)) {
            $db = new clsBanco();
            $gruda = '';
            $set = '';

            if (is_numeric($this->ref_serie_origem)) {
                $set .= "{$gruda}ref_serie_origem = '{$this->ref_serie_origem}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_serie_destino)) {
                $set .= "{$gruda}ref_serie_destino = '{$this->ref_serie_destino}'";
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
            if (is_numeric($this->ativo)) {
                $set .= "{$gruda}ativo = '{$this->ativo}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_serie_origem = '{$serie_origem_old}' AND ref_serie_destino = '{$serie_destino_old}'");

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
    public function lista($int_ref_serie_origem = null, $int_ref_serie_destino = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $int_ref_curso_origem = null, $int_ref_curso_destino = null, $int_ref_cod_instituicao = null)
    {
        $sql = "SELECT {$this->_campos_lista}, co.ref_cod_instituicao, so.ref_cod_curso as ref_curso_origem, sd.ref_cod_curso as ref_curso_destino FROM {$this->_tabela} ss, {$this->_schema}curso co, {$this->_schema}curso cd, {$this->_schema}serie so, {$this->_schema}serie sd";

        $whereAnd = ' AND ';
        $filtros = ' WHERE ss.ref_serie_origem = so.cod_serie AND ss.ref_serie_destino = sd.cod_serie AND so.ref_cod_curso = co.cod_curso AND sd.ref_cod_curso = cd.cod_curso ';

        if (is_numeric($int_ref_serie_origem)) {
            $filtros .= "{$whereAnd} ss.ref_serie_origem = '{$int_ref_serie_origem}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_serie_destino)) {
            $filtros .= "{$whereAnd} ss.ref_serie_destino = '{$int_ref_serie_destino}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_exc)) {
            $filtros .= "{$whereAnd} ss.ref_usuario_exc = '{$int_ref_usuario_exc}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_cad)) {
            $filtros .= "{$whereAnd} ss.ref_usuario_cad = '{$int_ref_usuario_cad}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_ini)) {
            $filtros .= "{$whereAnd} ss.data_cadastro >= '{$date_data_cadastro_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_fim)) {
            $filtros .= "{$whereAnd} ss.data_cadastro <= '{$date_data_cadastro_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_ini)) {
            $filtros .= "{$whereAnd} ss.data_exclusao >= '{$date_data_exclusao_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_fim)) {
            $filtros .= "{$whereAnd} ss.data_exclusao <= '{$date_data_exclusao_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_null($int_ativo) || $int_ativo) {
            $filtros .= "{$whereAnd} ss.ativo = '1'";
            $whereAnd = ' AND ';
        } else {
            $filtros .= "{$whereAnd} ss.ativo = '0'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_curso_origem)) {
            $filtros .= "{$whereAnd} so.ref_cod_curso = '$int_ref_curso_origem'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_curso_destino)) {
            $filtros .= "{$whereAnd} sd.ref_cod_curso = '$int_ref_curso_destino'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_instituicao)) {
            $filtros .= "{$whereAnd} co.ref_cod_instituicao = '$int_ref_cod_instituicao'";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} ss, {$this->_schema}curso co, {$this->_schema}curso cd, {$this->_schema}serie so, {$this->_schema}serie sd {$filtros}");

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
        if (is_numeric($this->ref_serie_origem) && is_numeric($this->ref_serie_destino)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} ss WHERE ss.ref_serie_origem = '{$this->ref_serie_origem}' AND ss.ref_serie_destino = '{$this->ref_serie_destino}'");
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
        if (is_numeric($this->ref_serie_origem) && is_numeric($this->ref_serie_destino)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_serie_origem = '{$this->ref_serie_origem}' AND ref_serie_destino = '{$this->ref_serie_destino}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Exclui uma sequência de série.
     *
     * @return bool
     *
     * @throws Exception
     */
    public function excluir()
    {
        if (is_numeric($this->ref_serie_origem) && is_numeric($this->ref_serie_destino)) {
            $db = new clsBanco();
            $result = $db->Consulta("DELETE FROM {$this->_tabela} WHERE ref_serie_origem = '{$this->ref_serie_origem}' AND ref_serie_destino = '{$this->ref_serie_destino}'");

            return boolval($result);
        }

        return false;
    }
}
