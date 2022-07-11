<?php

use iEducar\Legacy\Model;

class clsPmieducarReservaVaga extends Model
{
    public $cod_reserva_vaga;
    public $ref_ref_cod_escola;
    public $ref_ref_cod_serie;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_cod_aluno;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $nm_aluno;
    public $cpf_responsavel;

    /**
     * Construtor.
     *
     * @param int    $cod_reserva_vaga
     * @param int    $ref_ref_cod_escola
     * @param int    $ref_ref_cod_serie
     * @param int    $ref_usuario_exc
     * @param int    $ref_usuario_cad
     * @param int    $ref_cod_aluno
     * @param string $data_cadastro
     * @param string $data_exclusao
     * @param int    $ativo
     * @param string $nm_aluno
     * @param int    $cpf_responsavel
     */
    public function __construct(
        $cod_reserva_vaga = null,
        $ref_ref_cod_escola = null,
        $ref_ref_cod_serie = null,
        $ref_usuario_exc = null,
        $ref_usuario_cad = null,
        $ref_cod_aluno = null,
        $data_cadastro = null,
        $data_exclusao = null,
        $ativo = null,
        $nm_aluno = null,
        $cpf_responsavel = null
    ) {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = $this->_schema . 'reserva_vaga';

        $this->_campos_lista = $this->_todos_campos = 'rv.cod_reserva_vaga, rv.ref_ref_cod_escola, rv.ref_ref_cod_serie, rv.ref_usuario_exc, rv.ref_usuario_cad, rv.ref_cod_aluno, rv.data_cadastro, rv.data_exclusao, rv.ativo, rv.nm_aluno, rv.cpf_responsavel';

        if (is_numeric($ref_ref_cod_serie) && is_numeric($ref_ref_cod_escola)) {
            $this->ref_ref_cod_serie = $ref_ref_cod_serie;
            $this->ref_ref_cod_escola = $ref_ref_cod_escola;
        }

        if (is_numeric($ref_usuario_exc)) {
            $this->ref_usuario_exc = $ref_usuario_exc;
        }

        if (is_numeric($ref_usuario_cad)) {
            $this->ref_usuario_cad = $ref_usuario_cad;
        }

        if (is_numeric($ref_cod_aluno)) {
            $this->ref_cod_aluno = $ref_cod_aluno;
        }

        if (is_numeric($cod_reserva_vaga)) {
            $this->cod_reserva_vaga = $cod_reserva_vaga;
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

        if (is_string($nm_aluno)) {
            $this->nm_aluno = $nm_aluno;
        }

        if (is_numeric($cpf_responsavel)) {
            $this->cpf_responsavel = $cpf_responsavel;
        }
    }

    /**
     * Cria um novo registro.
     *
     * @return int|bool Retorna o valor da sequence ou FALSE em caso de erro.
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_ref_cod_escola) &&
            is_numeric($this->ref_ref_cod_serie) && is_numeric($this->ref_usuario_cad) &&
            (is_numeric($this->ref_cod_aluno) || (is_numeric($this->cpf_responsavel) && is_string($this->nm_aluno)))) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_ref_cod_escola)) {
                $campos .= "{$gruda}ref_ref_cod_escola";
                $valores .= "{$gruda}'{$this->ref_ref_cod_escola}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_ref_cod_serie)) {
                $campos .= "{$gruda}ref_ref_cod_serie";
                $valores .= "{$gruda}'{$this->ref_ref_cod_serie}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_usuario_cad)) {
                $campos .= "{$gruda}ref_usuario_cad";
                $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_aluno)) {
                $campos .= "{$gruda}ref_cod_aluno";
                $valores .= "{$gruda}'{$this->ref_cod_aluno}'";
                $gruda = ', ';
            }

            if (is_string($this->nm_aluno)) {
                $campos .= "{$gruda}nm_aluno";
                $valores .= "{$gruda}'{$this->nm_aluno}'";
                $gruda = ', ';
            }

            if (is_numeric($this->cpf_responsavel)) {
                $campos .= "{$gruda}cpf_responsavel";
                $valores .= "{$gruda}'{$this->cpf_responsavel}'";
                $gruda = ', ';
            }

            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';

            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId("{$this->_tabela}_cod_reserva_vaga_seq");
        }

        return false;
    }

    /**
     * Atualiza os dados de um registro.
     *
     * @return bool
     */
    public function edita()
    {
        if (is_numeric($this->cod_reserva_vaga)) {
            $db = new clsBanco();
            $gruda = '';
            $set = '';

            if (is_numeric($this->ref_ref_cod_escola)) {
                $set .= "{$gruda}ref_ref_cod_escola = '{$this->ref_ref_cod_escola}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_ref_cod_serie)) {
                $set .= "{$gruda}ref_ref_cod_serie = '{$this->ref_ref_cod_serie}'";
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

            if (is_numeric($this->ref_cod_aluno)) {
                $set .= "{$gruda}ref_cod_aluno = '{$this->ref_cod_aluno}'";
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

            if (is_string($this->nm_aluno)) {
                $set .= "{$gruda}nm_aluno = '{$this->nm_aluno}'";
                $gruda = ', ';
            }

            if (is_numeric($this->cpf_responsavel)) {
                $set .= "{$gruda}cpf_responsavel = '{$this->cpf_responsavel}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_reserva_vaga = '{$this->cod_reserva_vaga}'");

                return true;
            }
        }

        return false;
    }

    /**
     * Retorna uma lista de registros filtrados de acordo com os parâmetros.
     *
     * @return array|bool Retorna um array com registro(s) ou FALSE em caso de erro.
     *
     * @var int    $int_cod_reserva_vaga
     * @var int    $int_ref_ref_cod_escola
     * @var int    $int_ref_ref_cod_serie
     * @var int    $int_ref_usuario_exc
     * @var int    $int_ref_usuario_cad
     * @var int    $int_ref_cod_aluno
     * @var string $date_data_cadastro_ini
     * @var string $date_data_cadastro_fim
     * @var string $date_data_exclusao_ini
     * @var string $date_data_exclusao_fim
     * @var int    $int_ativo
     * @var int    $int_ref_cod_instituicao
     * @var int    $int_ref_cod_curso
     * @var string $str_nm_aluno
     * @var int    $int_cpf_responsavel
     *
     */
    public function lista(
        $int_cod_reserva_vaga = null,
        $int_ref_ref_cod_escola = null,
        $int_ref_ref_cod_serie = null,
        $int_ref_usuario_exc = null,
        $int_ref_usuario_cad = null,
        $int_ref_cod_aluno = null,
        $date_data_cadastro_ini = null,
        $date_data_cadastro_fim = null,
        $date_data_exclusao_ini = null,
        $date_data_exclusao_fim = null,
        $int_ativo = null,
        $int_ref_cod_instituicao = null,
        $int_ref_cod_curso = null,
        $str_nm_aluno = null,
        $int_cpf_responsavel = null
    ) {
        $sql = "SELECT {$this->_campos_lista}, c.ref_cod_instituicao, s.ref_cod_curso FROM {$this->_tabela} rv, {$this->_schema}serie s, {$this->_schema}curso c";

        $whereAnd = ' AND ';
        $filtros = ' WHERE rv.ref_ref_cod_serie = s.cod_serie AND s.ref_cod_curso = c.cod_curso ';

        if (is_numeric($int_cod_reserva_vaga)) {
            $filtros .= "{$whereAnd} rv.cod_reserva_vaga = '{$int_cod_reserva_vaga}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_ref_cod_escola)) {
            $filtros .= "{$whereAnd} rv.ref_ref_cod_escola = '{$int_ref_ref_cod_escola}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_ref_cod_serie)) {
            $filtros .= "{$whereAnd} rv.ref_ref_cod_serie = '{$int_ref_ref_cod_serie}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_usuario_exc)) {
            $filtros .= "{$whereAnd} rv.ref_usuario_exc = '{$int_ref_usuario_exc}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_usuario_cad)) {
            $filtros .= "{$whereAnd} rv.ref_usuario_cad = '{$int_ref_usuario_cad}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_aluno)) {
            $filtros .= "{$whereAnd} rv.ref_cod_aluno = '{$int_ref_cod_aluno}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_cadastro_ini)) {
            $filtros .= "{$whereAnd} rv.data_cadastro >= '{$date_data_cadastro_ini}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_cadastro_fim)) {
            $filtros .= "{$whereAnd} rv.data_cadastro <= '{$date_data_cadastro_fim}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_exclusao_ini)) {
            $filtros .= "{$whereAnd} rv.data_exclusao >= '{$date_data_exclusao_ini}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_exclusao_fim)) {
            $filtros .= "{$whereAnd} rv.data_exclusao <= '{$date_data_exclusao_fim}'";
            $whereAnd = ' AND ';
        }

        if (is_null($int_ativo) || $int_ativo) {
            $filtros .= "{$whereAnd} rv.ativo = '1'";
            $whereAnd = ' AND ';
        } else {
            $filtros .= "{$whereAnd} rv.ativo = '0'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_instituicao)) {
            $filtros .= "{$whereAnd} c.ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_curso)) {
            $filtros .= "{$whereAnd} s.ref_cod_curso = '{$int_ref_cod_curso}'";
            $whereAnd = ' AND ';
        }

        if (is_string($str_nm_aluno)) {
            $filtros .= "{$whereAnd} rv.nm_aluno ilike '%{$str_nm_aluno}%'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_cpf_responsavel)) {
            $filtros .= "{$whereAnd} rv.cpf_responsavel like '%{$int_cpf_responsavel}%'";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();
        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} rv, {$this->_schema}serie s, {$this->_schema}curso c {$filtros}");

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
     * @return array|bool
     */
    public function detalhe()
    {
        if (is_numeric($this->cod_reserva_vaga)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} rv WHERE rv.cod_reserva_vaga = '{$this->cod_reserva_vaga}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Retorna um array com os dados de um registro
     *
     * @return array|bool
     */
    public function existe()
    {
        if (is_numeric($this->cod_reserva_vaga)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_reserva_vaga = '{$this->cod_reserva_vaga}'");
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
        if (is_numeric($this->cod_reserva_vaga)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }
}
