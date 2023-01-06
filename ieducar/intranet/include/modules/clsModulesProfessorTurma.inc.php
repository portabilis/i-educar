<?php

use iEducar\Legacy\Model;

class clsModulesProfessorTurma extends Model
{
    public $id;

    public $ano;

    public $instituicao_id;

    public $servidor_id;

    public $turma_id;

    public $funcao_exercida;

    public $tipo_vinculo;

    public $permite_lancar_faltas_componente;

    public $turno_id;

    public $codUsuario;

    /**
     * Construtor.
     *
     * @param null $id
     * @param null $ano
     * @param null $instituicao_id
     * @param null $servidor_id
     * @param null $turma_id
     * @param null $funcao_exercida
     * @param null $tipo_vinculo
     * @param null $permite_lancar_faltas_componente
     * @param null $turno_id
     */
    public function __construct(
        $id = null,
        $ano = null,
        $instituicao_id = null,
        $servidor_id = null,
        $turma_id = null,
        $funcao_exercida = null,
        $tipo_vinculo = null,
        $permite_lancar_faltas_componente = null,
        $turno_id = null
    ) {
        $this->_schema = 'modules.';
        $this->_tabela = "{$this->_schema}professor_turma";

        $this->_campos_lista = $this->_todos_campos = ' pt.id, pt.ano, pt.instituicao_id, pt.servidor_id, pt.turma_id, pt.funcao_exercida, pt.tipo_vinculo, pt.permite_lancar_faltas_componente, pt.turno_id';

        if (is_numeric($id)) {
            $this->id = $id;
        }

        if (is_numeric($turma_id)) {
            $this->turma_id = $turma_id;
        }

        if (is_numeric($ano)) {
            $this->ano = $ano;
        }

        if (is_numeric($instituicao_id)) {
            $this->instituicao_id = $instituicao_id;
        }

        if (is_numeric($servidor_id)) {
            $this->servidor_id = $servidor_id;
        }

        if (is_numeric($funcao_exercida)) {
            $this->funcao_exercida = $funcao_exercida;
        }

        if (is_numeric($tipo_vinculo)) {
            $this->tipo_vinculo = $tipo_vinculo;
        }

        if (is_numeric($turno_id)) {
            $this->turno_id = $turno_id;
        }

        if (isset($permite_lancar_faltas_componente)) {
            $this->permite_lancar_faltas_componente = '1';
        } else {
            $this->permite_lancar_faltas_componente = '0';
        }
    }

    /**
     * Cria um novo registro.
     *
     * @return int|bool
     *
     * @throws Exception
     */
    public function cadastra()
    {
        if (
            is_numeric($this->turma_id)
            && is_numeric($this->funcao_exercida)
            && is_numeric($this->ano)
            && is_numeric($this->servidor_id)
            && is_numeric($this->instituicao_id)
        ) {
            $db = new clsBanco();
            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->instituicao_id)) {
                $campos .= "{$gruda}instituicao_id";
                $valores .= "{$gruda}'{$this->instituicao_id}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ano)) {
                $campos .= "{$gruda}ano";
                $valores .= "{$gruda}'{$this->ano}'";
                $gruda = ', ';
            }

            if (is_numeric($this->servidor_id)) {
                $campos .= "{$gruda}servidor_id";
                $valores .= "{$gruda}'{$this->servidor_id}'";
                $gruda = ', ';
            }

            if (is_numeric($this->turma_id)) {
                $campos .= "{$gruda}turma_id";
                $valores .= "{$gruda}'{$this->turma_id}'";
                $gruda = ', ';
            }

            if (is_numeric($this->funcao_exercida)) {
                $campos .= "{$gruda}funcao_exercida";
                $valores .= "{$gruda}'{$this->funcao_exercida}'";
                $gruda = ', ';
            }

            if (is_numeric($this->tipo_vinculo)) {
                $campos .= "{$gruda}tipo_vinculo";
                $valores .= "{$gruda}'{$this->tipo_vinculo}'";
                $gruda = ', ';
            }

            if (is_numeric($this->permite_lancar_faltas_componente)) {
                $campos .= "{$gruda}permite_lancar_faltas_componente";
                $valores .= "{$gruda}'{$this->permite_lancar_faltas_componente}'";
                $gruda = ', ';
            }

            if (is_numeric($this->turno_id)) {
                $campos .= "{$gruda}turno_id";
                $valores .= "{$gruda}'{$this->turno_id}'";
                $gruda = ', ';
            }

            $campos .= "{$gruda}updated_at";
            $valores .= "{$gruda} CURRENT_TIMESTAMP";
            $gruda = ', ';

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            $id = $db->InsertId("{$this->_tabela}_id_seq");
            $this->id = $id;

            return $id;
        }

        return false;
    }

    /**
     * Edita os dados de um registro.
     *
     * @return bool
     *
     * @throws Exception
     */
    public function edita()
    {
        if (
            is_numeric($this->id)
            && is_numeric($this->turma_id)
            && is_numeric($this->funcao_exercida)
            && is_numeric($this->ano)
            && is_numeric($this->servidor_id)
            && is_numeric($this->instituicao_id)
        ) {
            $db = new clsBanco();
            $set = '';
            $gruda = '';

            if (is_numeric($this->ano)) {
                $set .= "{$gruda}ano = '{$this->ano}'";
                $gruda = ', ';
            }

            if (is_numeric($this->instituicao_id)) {
                $set .= "{$gruda}instituicao_id = '{$this->instituicao_id}'";
                $gruda = ', ';
            }

            if (is_numeric($this->servidor_id)) {
                $set .= "{$gruda}servidor_id = '{$this->servidor_id}'";
                $gruda = ', ';
            }

            if (is_numeric($this->turma_id)) {
                $set .= "{$gruda}turma_id = '{$this->turma_id}'";
                $gruda = ', ';
            }

            if (is_numeric($this->funcao_exercida)) {
                $set .= "{$gruda}funcao_exercida = '{$this->funcao_exercida}'";
                $gruda = ', ';
            }

            if (is_numeric($this->tipo_vinculo)) {
                $set .= "{$gruda}tipo_vinculo = '{$this->tipo_vinculo}'";
                $gruda = ', ';
            } elseif (is_null($this->tipo_vinculo)) {
                $set .= "{$gruda}tipo_vinculo = NULL";
                $gruda = ', ';
            }

            if (is_numeric($this->permite_lancar_faltas_componente)) {
                $set .= "{$gruda}permite_lancar_faltas_componente = '{$this->permite_lancar_faltas_componente}'";
                $gruda = ', ';
            }

            if (is_numeric($this->turno_id)) {
                $set .= "{$gruda}turno_id = '{$this->turno_id}'";
                $gruda = ', ';
            } elseif (is_null($this->turno_id)) {
                $set .= "{$gruda}turno_id = NULL";
                $gruda = ', ';
            }

            $set .= "{$gruda}updated_at = CURRENT_TIMESTAMP";
            $gruda = ', ';

            if ($set) {
                $detalheAntigo = $this->detalhe();
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE id = '{$this->id}'");
                $detalheAtual = $this->detalhe();

                return true;
            }
        }

        return false;
    }

    /**
     * Retorna uma lista de registros filtrados de acordo com os parâmetros.
     *
     * @param null $servidor_id
     * @param null $instituicao_id
     * @param null $ano
     * @param null $ref_cod_escola
     * @param null $ref_cod_curso
     * @param null $ref_cod_serie
     * @param null $ref_cod_turma
     * @param null $funcao_exercida
     * @param null $tipo_vinculo
     *
     * @return array|bool
     *
     * @throws Exception
     */
    public function lista(
        $servidor_id = null,
        $instituicao_id = null,
        $ano = null,
        $ref_cod_escola = null,
        $ref_cod_curso = null,
        $ref_cod_serie = null,
        $ref_cod_turma = null,
        $funcao_exercida = null,
        $tipo_vinculo = null
    ) {
        $sql = "

            SELECT
                {$this->_campos_lista},
                t.nm_turma,
                t.cod_turma as ref_cod_turma,
                t.ref_ref_cod_serie as ref_cod_serie,
                s.nm_serie,
                t.ref_cod_curso,
                c.nm_curso,
                t.ref_ref_cod_escola as ref_cod_escola,
                p.nome as nm_escola
            FROM {$this->_tabela} pt
        ";
        $filtros = ' , pmieducar.turma t, pmieducar.serie s, pmieducar.curso c, pmieducar.escola e, cadastro.pessoa p WHERE pt.turma_id = t.cod_turma AND t.ref_ref_cod_serie = s.cod_serie AND s.ref_cod_curso = c.cod_curso
                  AND t.ref_ref_cod_escola = e.cod_escola AND e.ref_idpes = p.idpes ';

        $whereAnd = ' AND ';

        if (is_numeric($servidor_id)) {
            $filtros .= "{$whereAnd} pt.servidor_id = '{$servidor_id}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($instituicao_id)) {
            $filtros .= "{$whereAnd} pt.instituicao_id = '{$instituicao_id}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($ano)) {
            $filtros .= "{$whereAnd} pt.ano = '{$ano}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($ref_cod_escola)) {
            $filtros .= "{$whereAnd} t.ref_ref_cod_escola = '{$ref_cod_escola}'";
            $whereAnd = ' AND ';
        } elseif ($this->codUsuario) {
            $filtros .= "{$whereAnd} EXISTS (SELECT 1
                                         FROM pmieducar.escola_usuario
                                        WHERE escola_usuario.ref_cod_escola = t.ref_ref_cod_escola
                                          AND escola_usuario.ref_cod_usuario = '{$this->codUsuario}')";
            $whereAnd = ' AND ';
        }

        if (is_numeric($ref_cod_curso)) {
            $filtros .= "{$whereAnd} t.ref_cod_curso = '{$ref_cod_curso}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($ref_cod_serie)) {
            $filtros .= "{$whereAnd} t.ref_ref_cod_serie = '{$ref_cod_serie}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($ref_cod_turma)) {
            $filtros .= "{$whereAnd} t.cod_turma = '{$ref_cod_turma}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($funcao_exercida)) {
            $filtros .= "{$whereAnd} pt.funcao_exercida = '{$funcao_exercida}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($tipo_vinculo)) {
            $filtros .= "{$whereAnd} pt.tipo_vinculo = '{$tipo_vinculo}'";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista)) + 8;
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} pt {$filtros}");

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
     * @return array|bool
     *
     * @throws Exception
     */
    public function detalhe()
    {
        if (is_numeric($this->id)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_campos_lista}, t.nm_turma, s.nm_serie, c.nm_curso, p.nome as nm_escola
                     FROM {$this->_tabela} pt, pmieducar.turma t, pmieducar.serie s, pmieducar.curso c,
                     pmieducar.escola e, cadastro.pessoa p
                     WHERE pt.turma_id = t.cod_turma AND t.ref_ref_cod_serie = s.cod_serie AND s.ref_cod_curso = c.cod_curso
                     AND t.ref_ref_cod_escola = e.cod_escola AND e.ref_idpes = p.idpes AND id = '{$this->id}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Retorna um array com os dados de um registro.
     *
     * @return array|false
     *
     * @throws Exception
     */
    public function existe()
    {
        if (is_numeric($this->id)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} pt WHERE id = '{$this->id}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * @return int|bool
     */
    public function existe2()
    {
        if (
            is_numeric($this->ano)
            && is_numeric($this->instituicao_id)
            && is_numeric($this->servidor_id)
            && is_numeric($this->turma_id)
        ) {
            $db = new clsBanco();
            $sql = "SELECT id FROM {$this->_tabela} pt WHERE ano = '{$this->ano}' AND turma_id = '{$this->turma_id}'
               AND instituicao_id = '{$this->instituicao_id}' AND servidor_id = '{$this->servidor_id}' ";

            if (is_numeric($this->id)) {
                $sql .= " AND id <> {$this->id}";
            }

            return $db->UnicoCampo($sql);
        }

        return false;
    }

    /**
     * Exclui um registro.
     *
     * @return bool
     *
     * @throws Exception
     */
    public function excluir()
    {
        if (is_numeric($this->id)) {
            $detalhe = $this->detalhe();
            $sql = "DELETE FROM {$this->_tabela} pt WHERE id = '{$this->id}'";
            $db = new clsBanco();
            $db->Consulta($sql);

            return true;
        }

        return false;
    }

    public function gravaComponentes($professor_turma_id, $componentes)
    {
        $componentesAntigos = $this->retornaComponentesVinculados($professor_turma_id);
        $this->excluiComponentes($professor_turma_id);
        $db = new clsBanco();
        foreach ($componentes as $componente) {
            $db->Consulta("INSERT INTO modules.professor_turma_disciplina VALUES ({$professor_turma_id},{$componente})");
        }
        $componentesNovos = $this->retornaComponentesVinculados($professor_turma_id);
        $this->auditaComponentesVinculados($professor_turma_id, $componentesAntigos, $componentesNovos);
    }

    public function excluiComponentes($professor_turma_id)
    {
        $db = new clsBanco();
        $db->Consulta("DELETE FROM modules.professor_turma_disciplina WHERE professor_turma_id = {$professor_turma_id}");
    }

    public function retornaComponentesVinculados($professor_turma_id)
    {
        $componentesVinculados = [];
        $sql = "SELECT componente_curricular_id
                  FROM modules.professor_turma_disciplina
                 WHERE professor_turma_id = {$professor_turma_id}";
        $db = new clsBanco();
        $db->Consulta($sql);
        while ($db->ProximoRegistro()) {
            $tupla = $db->Tupla();
            $componentesVinculados[] = $tupla['componente_curricular_id'];
        }

        return $componentesVinculados;
    }

    public function pegarServidorId($servidor_id)
    {
        $servidor_id = [];
        $sql = "SELECT servidor_id FROM modules.professor_turma WHERE servidor_id = {$servidor_id}";
        $db = new clsBanco();
        $db->Consulta($sql);
        while($db->ProximoRegistro()){
            $tupla = $db->Tupla();
            $servidor_id[] = $tupla['servidor_id'];
        }
        return $servidor_id;

    }

    private function auditaComponentesVinculados($professor_turma_id, $componentesAntigos, $componentesNovos)
    {
        $componentesExcluidos = array_diff($componentesAntigos, $componentesNovos);
        $componentesAdicionados = array_diff($componentesNovos, $componentesAntigos);

        foreach ($componentesExcluidos as $componente) {
            $componente = [
                'componente_curricular_id' => $componente,
                'nome' => $this->retornaNomeDoComponente($componente)
            ];
        }

        foreach ($componentesAdicionados as $componente) {
            $componente = [
                'componente_curricular_id' => $componente,
                'nome' => $this->retornaNomeDoComponente($componente)
            ];
        }
    }

    public function retornaNomeDoComponente($idComponente)
    {
        $mapperComponente = new ComponenteCurricular_Model_ComponenteDataMapper;
        $componente = $mapperComponente->find(['id' => $idComponente]);

        return $componente->nome;
    }
}
