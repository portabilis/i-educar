<?php

use iEducar\Legacy\Model;

require_once 'include/pmieducar/geral.inc.php';

class clsPmieducarDispensaDisciplina extends Model
{
    public $ref_cod_matricula;
    public $ref_cod_serie;
    public $ref_cod_escola;
    public $ref_cod_disciplina;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_cod_tipo_dispensa;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $observacao;
    public $cod_dispensa;

    public function __construct(
        $ref_cod_matricula = null,
        $ref_cod_serie = null,
        $ref_cod_escola = null,
        $ref_cod_disciplina = null,
        $ref_usuario_exc = null,
        $ref_usuario_cad = null,
        $ref_cod_tipo_dispensa = null,
        $data_cadastro = null,
        $data_exclusao = null,
        $ativo = null,
        $observacao = null,
        $cod_dispensa = null
    ) {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = $this->_schema . 'dispensa_disciplina';

        $this->_campos_lista = $this->_todos_campos = '
            ref_cod_matricula,
            ref_cod_serie,
            ref_cod_escola,
            ref_cod_disciplina,
            ref_usuario_exc,
            ref_usuario_cad,
            ref_cod_tipo_dispensa,
            data_cadastro,
            data_exclusao,
            ativo,
            observacao,
            cod_dispensa
        ';

        if (is_numeric($ref_usuario_exc)) {
            $usuario = new clsPmieducarUsuario($ref_usuario_exc);
            if ($usuario->existe()) {
                $this->ref_usuario_exc = $ref_usuario_exc;
            }
        }

        if (is_numeric($ref_usuario_cad)) {
            $usuario = new clsPmieducarUsuario($ref_usuario_cad);
            if ($usuario->existe()) {
                $this->ref_usuario_cad = $ref_usuario_cad;
            }
        }

        if (is_numeric($ref_cod_matricula)) {
            $matricula = new clsPmieducarMatricula($ref_cod_matricula);
            if ($matricula->existe()) {
                $this->ref_cod_matricula = $ref_cod_matricula;
            }
        }

        if (is_numeric($ref_cod_tipo_dispensa)) {
            $tipoDispensa = new clsPmieducarTipoDispensa($ref_cod_tipo_dispensa);
            if ($tipoDispensa->existe()) {
                $this->ref_cod_tipo_dispensa = $ref_cod_tipo_dispensa;
            }
        }

        if (is_numeric($ref_cod_disciplina) && is_numeric($ref_cod_escola) && is_numeric($ref_cod_serie)) {
            require_once 'ComponenteCurricular/Model/AnoEscolarDataMapper.php';
            $anoEscolarMapper = new ComponenteCurricular_Model_AnoEscolarDataMapper();
            $componenteAnos = $anoEscolarMapper->findAll([], [
                'componenteCurricular' => $ref_cod_disciplina,
                'anoEscolar' => $ref_cod_serie
            ]);

            if (1 == count($componenteAnos)) {
                $this->ref_cod_disciplina = $ref_cod_disciplina;
                $this->ref_cod_serie = $ref_cod_serie;
                $this->ref_cod_escola = $ref_cod_escola;
            }
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

        if (is_string($observacao)) {
            $this->observacao = $observacao;
        }

        if (is_numeric($cod_dispensa)) {
            $this->cod_dispensa = $cod_dispensa;
        }
    }

    /**
     * Cria um novo registro.
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_serie) &&
            is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_disciplina) &&
            is_numeric($this->ref_usuario_cad) && is_numeric($this->ref_cod_tipo_dispensa)
        ) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_matricula)) {
                $campos .= "{$gruda}ref_cod_matricula";
                $valores .= "{$gruda}'{$this->ref_cod_matricula}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_serie)) {
                $campos .= "{$gruda}ref_cod_serie";
                $valores .= "{$gruda}'{$this->ref_cod_serie}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_escola)) {
                $campos .= "{$gruda}ref_cod_escola";
                $valores .= "{$gruda}'{$this->ref_cod_escola}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_disciplina)) {
                $campos .= "{$gruda}ref_cod_disciplina";
                $valores .= "{$gruda}'{$this->ref_cod_disciplina}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_usuario_cad)) {
                $campos .= "{$gruda}ref_usuario_cad";
                $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_tipo_dispensa)) {
                $campos .= "{$gruda}ref_cod_tipo_dispensa";
                $valores .= "{$gruda}'{$this->ref_cod_tipo_dispensa}'";
                $gruda = ', ';
            }

            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';

            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';

            if (is_string($this->observacao)) {
                $observacao = $db->escapeString($this->observacao);
                $campos .= "{$gruda}observacao";
                $valores .= "{$gruda}'{$observacao}'";
                $gruda = ', ';
            }

            $sql = "INSERT INTO {$this->_tabela} ($campos) VALUES ($valores)";
            $db->Consulta($sql);
            $id = $db->InsertId("{$this->_tabela}_cod_dispensa_seq");
            $this->id = $id;
            $auditoria = new clsModulesAuditoriaGeral('dispensa_disciplina', $this->pessoa_logada, $id);
            $auditoria->inclusao($this->detalhe());

            return $id;
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
        if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_serie) &&
            is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_disciplina) &&
            is_numeric($this->ref_usuario_exc)) {
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

            if (is_numeric($this->ref_cod_tipo_dispensa)) {
                $set .= "{$gruda}ref_cod_tipo_dispensa = '{$this->ref_cod_tipo_dispensa}'";
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

            if (is_string($this->observacao)) {
                $observacao = $db->escapeString($this->observacao);
                $set .= "{$gruda}observacao = '{$observacao}'";
                $gruda = ', ';
            }

            if ($set) {
                $detalheAntigo = $this->detalhe();
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_matricula = '{$this->ref_cod_matricula}' AND ref_cod_serie = '{$this->ref_cod_serie}' AND ref_cod_escola = '{$this->ref_cod_escola}' AND ref_cod_disciplina = '{$this->ref_cod_disciplina}'");
                $detalheAtual = $this->detalhe();
                $auditoria = new clsModulesAuditoriaGeral('dispensa_disciplina', $this->pessoa_logada, $this->id);
                $auditoria->alteracao($detalheAntigo, $detalheAtual);

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
        $int_ref_cod_matricula = null,
        $int_ref_cod_serie = null,
        $int_ref_cod_escola = null,
        $int_ref_cod_disciplina = null,
        $int_ref_usuario_exc = null,
        $int_ref_usuario_cad = null,
        $int_ref_cod_tipo_dispensa = null,
        $date_data_cadastro_ini = null,
        $date_data_cadastro_fim = null,
        $date_data_exclusao_ini = null,
        $date_data_exclusao_fim = null,
        $int_ativo = null,
        $str_observacao = null
    ) {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_ref_cod_matricula)) {
            $filtros .= "{$whereAnd} ref_cod_matricula = '{$int_ref_cod_matricula}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_serie)) {
            $filtros .= "{$whereAnd} ref_cod_serie = '{$int_ref_cod_serie}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_escola)) {
            $filtros .= "{$whereAnd} ref_cod_escola = '{$int_ref_cod_escola}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_disciplina)) {
            $filtros .= "{$whereAnd} ref_cod_disciplina = '{$int_ref_cod_disciplina}'";
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

        if (is_numeric($int_ref_cod_tipo_dispensa)) {
            $filtros .= "{$whereAnd} ref_cod_tipo_dispensa = '{$int_ref_cod_tipo_dispensa}'";
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

        if (is_string($str_observacao)) {
            $filtros .= "{$whereAnd} observacao LIKE '%{$str_observacao}%'";
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

    public function disciplinaDispensadaEtapa(
        $int_ref_cod_matricula = null,
        $int_ref_cod_serie = null,
        $int_ref_cod_escola = null,
        $int_etapa = null,
        $ignorarDispensasParciais = false
    ) {
        $sql = "SELECT {$this->_campos_lista}, etapa
                  FROM {$this->_tabela}
                 INNER JOIN pmieducar.dispensa_etapa ON (dispensa_etapa.ref_cod_dispensa = dispensa_disciplina.cod_dispensa)";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_ref_cod_matricula)) {
            $filtros .= "{$whereAnd} ref_cod_matricula = '{$int_ref_cod_matricula}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_serie)) {
            $filtros .= "{$whereAnd} ref_cod_serie = '{$int_ref_cod_serie}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_escola)) {
            $filtros .= "{$whereAnd} ref_cod_escola = '{$int_ref_cod_escola}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_etapa)) {
            $filtros .= "{$whereAnd} etapa = '{$int_etapa}'";
            $whereAnd = ' AND ';
        }

        if ($ignorarDispensasParciais) {
            $filtros .= "{$whereAnd}
                (SELECT COALESCE((
                    SELECT count(1)
                    FROM pmieducar.curso as c
                    INNER JOIN pmieducar.ano_letivo_modulo as anm
                    ON anm.ref_ref_cod_escola = matricula.ref_ref_cod_escola
                    AND anm.ref_ano = matricula.ano
                    WHERE c.padrao_ano_escolar = 1
                    AND matricula.ref_cod_curso = c.cod_curso
                ),0) +
                COALESCE((
                    SELECT COUNT(1)
                    FROM pmieducar.turma as t
                    INNER JOIN pmieducar.curso as c
                    ON t.ref_cod_curso = c.cod_curso
                    INNER JOIN pmieducar.turma_modulo as tm
                    ON tm.ref_cod_turma = t.cod_turma
                    WHERE c.padrao_ano_escolar = 0
                    AND t.cod_turma = (
                        SELECT matricula_turma.ref_cod_turma FROM pmieducar.matricula_turma
                        WHERE matricula_turma.ref_cod_matricula = matricula.cod_matricula
                        ORDER BY ativo DESC, data_enturmacao DESC
                        LIMIT 1
                    )
                ),0) AS etapas
            FROM pmieducar.matricula
            WHERE cod_matricula = ref_cod_matricula
            )
             = (
                SELECT count(1)
                FROM pmieducar.dispensa_etapa sde
                WHERE sde.ref_cod_dispensa = dispensa_disciplina.cod_dispensa
            )
            ";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0)
                                       FROM {$this->_tabela}
                                      INNER JOIN pmieducar.dispensa_etapa ON (dispensa_etapa.ref_cod_dispensa = dispensa_disciplina.cod_dispensa)
                                      {$filtros}");
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
        if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_serie) &&
            is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_disciplina)
        ) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_matricula = '{$this->ref_cod_matricula}' AND ref_cod_serie = '{$this->ref_cod_serie}' AND ref_cod_escola = '{$this->ref_cod_escola}' AND ref_cod_disciplina = '{$this->ref_cod_disciplina}'");
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
        if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_serie) &&
            is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_disciplina)
        ) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_cod_matricula = '{$this->ref_cod_matricula}' AND ref_cod_serie = '{$this->ref_cod_serie}' AND ref_cod_escola = '{$this->ref_cod_escola}' AND ref_cod_disciplina = '{$this->ref_cod_disciplina}'");
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
        if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_serie) &&
            is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_disciplina) &&
            is_numeric($this->ref_usuario_exc)
        ) {
            $detalhe = $this->detalhe();
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->_tabela} WHERE ref_cod_matricula = '{$this->ref_cod_matricula}' AND ref_cod_disciplina = '{$this->ref_cod_disciplina}'");
            $auditoria = new clsModulesAuditoriaGeral('dispensa_disciplina', $this->pessoa_logada, $this->id);
            $auditoria->exclusao($detalhe);

            return $this->edita();
        }

        return false;
    }
}
