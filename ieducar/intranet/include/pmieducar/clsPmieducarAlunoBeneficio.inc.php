<?php

use iEducar\Legacy\Model;

class clsPmieducarAlunoBeneficio extends Model
{
    public $cod_aluno_beneficio;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_beneficio;
    public $desc_beneficio;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function __construct($cod_aluno_beneficio = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $nm_beneficio = null, $desc_beneficio = null, $data_cadastro = null, $data_exclusao = null, $ativo = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}aluno_beneficio";

        $this->_campos_lista = $this->_todos_campos = 'cod_aluno_beneficio, ref_usuario_exc, ref_usuario_cad, nm_beneficio, desc_beneficio, data_cadastro, data_exclusao, ativo';

        if (is_numeric($ref_usuario_exc)) {
            $this->ref_usuario_exc = $ref_usuario_exc;
        }
        if (is_numeric($ref_usuario_cad)) {
            $this->ref_usuario_cad = $ref_usuario_cad;
        }

        if (is_numeric($cod_aluno_beneficio)) {
            $this->cod_aluno_beneficio = $cod_aluno_beneficio;
        }
        if (is_string($nm_beneficio)) {
            $this->nm_beneficio = $nm_beneficio;
        }
        if (is_string($desc_beneficio)) {
            $this->desc_beneficio = $desc_beneficio;
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
        if (is_numeric($this->ref_usuario_cad) && is_string($this->nm_beneficio)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_usuario_cad)) {
                $campos .= "{$gruda}ref_usuario_cad";
                $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_string($this->nm_beneficio)) {
                $nm_beneficio = $db->escapeString($this->nm_beneficio);
                $campos .= "{$gruda}nm_beneficio";
                $valores .= "{$gruda}'{$nm_beneficio}'";
                $gruda = ', ';
            }
            if (is_string($this->desc_beneficio)) {
                $desc_beneficio = $db->escapeString($this->desc_beneficio);
                $campos .= "{$gruda}desc_beneficio";
                $valores .= "{$gruda}'{$desc_beneficio}'";
                $gruda = ', ';
            }
            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';
            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId("{$this->_tabela}_cod_aluno_beneficio_seq");
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
        if (is_numeric($this->cod_aluno_beneficio) && is_numeric($this->ref_usuario_exc)) {
            $db = new clsBanco();

            $set = '';

            if (is_numeric($this->ref_usuario_exc)) {
                $set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_cad)) {
                $set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_string($this->nm_beneficio)) {
                $nm_beneficio = $db->escapeString($this->nm_beneficio);
                $set .= "{$gruda}nm_beneficio = '{$nm_beneficio}'";
                $gruda = ', ';
            }
            if (is_string($this->desc_beneficio)) {
                $desc_beneficio = $db->escapeString($this->desc_beneficio);
                $set .= "{$gruda}desc_beneficio = '{$desc_beneficio}'";
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
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_aluno_beneficio = '{$this->cod_aluno_beneficio}'");

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
    public function lista(
        $int_cod_aluno_beneficio = null,
        $int_ref_usuario_exc = null,
        $int_ref_usuario_cad = null,
        $str_nm_beneficio = null,
        $str_desc_beneficio = null,
        $date_data_cadastro_ini = null,
        $date_data_cadastro_fim = null,
        $date_data_exclusao_ini = null,
        $date_data_exclusao_fim = null,
        $int_ativo = null,
        $int_codigo_aluno = null
    ) {
        $db = new clsBanco();

        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        if ($int_codigo_aluno) {
            $sql .= ' INNER JOIN pmieducar.aluno_aluno_beneficio ON (aluno_aluno_beneficio.aluno_beneficio_id = aluno_beneficio.cod_aluno_beneficio) ';
        }

        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_cod_aluno_beneficio)) {
            $filtros .= "{$whereAnd} cod_aluno_beneficio = '{$int_cod_aluno_beneficio}'";
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
        if (is_string($str_nm_beneficio)) {
            $str_nome_beneficio = $db->escapeString($str_nm_beneficio);
            $filtros .= "{$whereAnd} nm_beneficio LIKE '%{$str_nome_beneficio}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_desc_beneficio)) {
            $filtros .= "{$whereAnd} desc_beneficio LIKE '%{$str_desc_beneficio}%'";
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
        if ($int_codigo_aluno) {
            $filtros .= "{$whereAnd} aluno_id = {$int_codigo_aluno} ";
            $whereAnd = ' AND ';
        }

        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        if ($int_codigo_aluno) {
            $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} INNER JOIN pmieducar.aluno_aluno_beneficio ON (aluno_aluno_beneficio.aluno_beneficio_id = aluno_beneficio.cod_aluno_beneficio) {$filtros}");
        } else {
            $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} {$filtros}");
        }

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
        if (is_numeric($this->cod_aluno_beneficio)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_aluno_beneficio = '{$this->cod_aluno_beneficio}'");
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
        if (is_numeric($this->cod_aluno_beneficio)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_aluno_beneficio = '{$this->cod_aluno_beneficio}'");
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
        if (is_numeric($this->cod_aluno_beneficio) && is_numeric($this->ref_usuario_exc)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }

    public function listaBeneficiosPorAluno($alunoId)
    {
        $db = new clsBanco();
        $db->Consulta("SELECT aluno_beneficio_id FROM pmieducar.aluno_aluno_beneficio WHERE aluno_id = {$alunoId} ");

        while ($db->ProximoRegistro()) {
            $resultado[] = $db->Tupla();
        }

        if (count($resultado)) {
            return $resultado;
        }

        return false;
    }

    public function deletaBeneficiosDoAluno($alunoId)
    {
        $db = new clsBanco();
        $db->Consulta("DELETE FROM pmieducar.aluno_aluno_beneficio WHERE aluno_id = {$alunoId}");

        return true;
    }

    public function cadastraBeneficiosDoAluno($alunoId, $beneficioId)
    {
        $db = new clsBanco();
        $db->Consulta("INSERT INTO pmieducar.aluno_aluno_beneficio (aluno_id, aluno_beneficio_id) VALUES ({$alunoId},{$beneficioId})");

        return true;
    }
}
